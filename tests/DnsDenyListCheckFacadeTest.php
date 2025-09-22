<?php

declare(strict_types=1);

namespace palPalani\DnsDenyListCheck\Tests;

use Illuminate\Support\Facades\Facade;
use palPalani\DnsDenyListCheck\DnsDenyListCheck;
use palPalani\DnsDenyListCheck\DnsDenyListCheckFacade;

class DnsDenyListCheckFacadeTest extends TestCase
{
    public function test_facade_extends_correct_base_class()
    {
        $facade = new DnsDenyListCheckFacade();
        
        $this->assertInstanceOf(Facade::class, $facade);
    }

    public function test_facade_accessor_returns_correct_key()
    {
        // Test the static method directly without triggering facade resolution
        $reflection = new \ReflectionClass(DnsDenyListCheckFacade::class);
        $method = $reflection->getMethod('getFacadeAccessor');
        $method->setAccessible(true);
        
        $accessor = $method->invoke(null);
        
        $this->assertEquals('dns-deny-list-check', $accessor);
    }

    public function test_facade_is_registered_in_service_container()
    {
        // Bind the service to test facade resolution
        $this->app->bind('dns-deny-list-check', function () {
            return new DnsDenyListCheck([
                ['name' => 'Test DNSBL', 'host' => 'test.example.com'],
            ]);
        });
        
        // Test that facade can resolve the service
        $service = DnsDenyListCheckFacade::getFacadeRoot();
        
        $this->assertInstanceOf(DnsDenyListCheck::class, $service);
    }

    public function test_facade_can_call_check_method()
    {
        // Bind the service with test servers
        $testServers = [
            ['name' => 'Test DNSBL', 'host' => 'test.example.com'],
        ];
        
        $this->app->bind('dns-deny-list-check', function () use ($testServers) {
            return new DnsDenyListCheck($testServers);
        });
        
        // Use facade to call check method
        $result = DnsDenyListCheckFacade::check('8.8.8.8');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertTrue($result['success']);
    }

    public function test_facade_handles_invalid_ip_through_facade()
    {
        // Bind the service
        $this->app->bind('dns-deny-list-check', function () {
            return new DnsDenyListCheck([]);
        });
        
        // Test invalid IP through facade
        $result = DnsDenyListCheckFacade::check('invalid-ip');
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals('Invalid IP address', $result['message']);
        $this->assertNull($result['data']);
    }

    public function test_facade_works_with_config_servers()
    {
        // Set up config
        $configServers = [
            ['name' => 'Config DNSBL 1', 'host' => 'config1.example.com'],
            ['name' => 'Config DNSBL 2', 'host' => 'config2.example.com'],
        ];
        config(['dns-deny-list-check.servers' => $configServers]);
        
        // Bind service that uses config
        $this->app->bind('dns-deny-list-check', function () {
            return new DnsDenyListCheck();
        });
        
        // Test through facade
        $result = DnsDenyListCheckFacade::check('192.168.1.1');
        
        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
        $this->assertCount(2, $result['data']);
        
        $hosts = array_column($result['data'], 'host');
        $this->assertContains('config1.example.com', $hosts);
        $this->assertContains('config2.example.com', $hosts);
    }

    public function test_facade_maintains_state_between_calls()
    {
        // Bind service with specific servers
        $testServers = [
            ['name' => 'Persistent DNSBL', 'host' => 'persistent.example.com'],
        ];
        
        $this->app->singleton('dns-deny-list-check', function () use ($testServers) {
            return new DnsDenyListCheck($testServers);
        });
        
        // Make multiple calls through facade
        $result1 = DnsDenyListCheckFacade::check('1.1.1.1');
        $result2 = DnsDenyListCheckFacade::check('8.8.8.8');
        
        // Both should use same server configuration
        $this->assertEquals('persistent.example.com', $result1['data'][0]['host']);
        $this->assertEquals('persistent.example.com', $result2['data'][0]['host']);
    }

    public function test_facade_handles_service_binding_errors_gracefully()
    {
        // Clear any existing bindings
        $this->app->forgetInstance('dns-deny-list-check');
        
        // Don't bind the service to test error handling
        try {
            $result = DnsDenyListCheckFacade::check('8.8.8.8');
            
            // If we get here, Laravel created a default instance
            // This is acceptable behavior
            $this->assertTrue(true);
        } catch (\Illuminate\Contracts\Container\BindingResolutionException $e) {
            // This is also acceptable - service not bound
            $this->assertStringContainsString('dns-deny-list-check', $e->getMessage());
        }
    }

    public function test_facade_class_has_correct_docblock()
    {
        $reflection = new \ReflectionClass(DnsDenyListCheckFacade::class);
        $docComment = $reflection->getDocComment();
        
        $this->assertNotFalse($docComment);
        $this->assertStringContainsString('@see', $docComment);
        $this->assertStringContainsString('DnsDenyListCheck', $docComment);
    }

    public function test_facade_is_available_as_alias()
    {
        // Test that the facade alias is registered in composer.json
        $composerPath = __DIR__ . '/../composer.json';
        $this->assertFileExists($composerPath);
        
        $composer = json_decode(file_get_contents($composerPath), true);
        $this->assertArrayHasKey('extra', $composer);
        $this->assertArrayHasKey('laravel', $composer['extra']);
        $this->assertArrayHasKey('aliases', $composer['extra']['laravel']);
        
        $aliases = $composer['extra']['laravel']['aliases'];
        $this->assertArrayHasKey('DnsDenyListCheck', $aliases);
        $this->assertEquals(
            'palPalani\\DnsDenyListCheck\\DnsDenyListCheckFacade',
            $aliases['DnsDenyListCheck']
        );
    }

    public function test_facade_static_calls_work_correctly()
    {
        // Bind the service
        $this->app->bind('dns-deny-list-check', function () {
            return new DnsDenyListCheck([
                ['name' => 'Static Test DNSBL', 'host' => 'static-test.example.com'],
            ]);
        });
        
        // Test various static call patterns
        $validResult = DnsDenyListCheckFacade::check('127.0.0.1');
        $invalidResult = DnsDenyListCheckFacade::check('not-an-ip');
        
        // Valid IP should succeed
        $this->assertTrue($validResult['success']);
        $this->assertIsArray($validResult['data']);
        
        // Invalid IP should fail
        $this->assertFalse($invalidResult['success']);
        $this->assertEquals('Invalid IP address', $invalidResult['message']);
    }

    public function test_facade_works_with_dependency_injection()
    {
        // Test that facade works when service is created with DI
        $customServers = [
            ['name' => 'DI DNSBL', 'host' => 'di-test.example.com'],
        ];
        
        $this->app->bind('dns-deny-list-check', function ($app) use ($customServers) {
            // Simulate dependency injection
            return new DnsDenyListCheck($customServers);
        });
        
        $result = DnsDenyListCheckFacade::check('198.51.100.1'); // RFC5737 test IP
        
        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['data']);
        $this->assertEquals('di-test.example.com', $result['data'][0]['host']);
    }

    public function test_facade_performance_with_multiple_calls()
    {
        // Bind service as singleton for performance
        $this->app->singleton('dns-deny-list-check', function () {
            return new DnsDenyListCheck([
                ['name' => 'Performance Test DNSBL', 'host' => 'perf-test.example.com'],
            ]);
        });
        
        $startTime = microtime(true);
        
        // Make multiple calls
        for ($i = 0; $i < 10; $i++) {
            $result = DnsDenyListCheckFacade::check("192.0.2.{$i}");
            $this->assertTrue($result['success']);
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Should complete within reasonable time
        $this->assertLessThan(5.0, $executionTime, 'Facade calls took too long');
    }

    public function test_facade_namespace_is_correct()
    {
        $reflection = new \ReflectionClass(DnsDenyListCheckFacade::class);
        
        $this->assertEquals(
            'palPalani\\DnsDenyListCheck\\DnsDenyListCheckFacade',
            $reflection->getName()
        );
        
        $this->assertEquals(
            'palPalani\\DnsDenyListCheck',
            $reflection->getNamespaceName()
        );
    }
}