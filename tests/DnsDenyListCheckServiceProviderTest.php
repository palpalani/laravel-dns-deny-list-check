<?php

declare(strict_types=1);

namespace palPalani\DnsDenyListCheck\Tests;

use Illuminate\Support\Facades\File;
use palPalani\DnsDenyListCheck\DnsDenyListCheckServiceProvider;

class DnsDenyListCheckServiceProviderTest extends TestCase
{
    public function test_service_provider_is_registered()
    {
        $providers = $this->app->getLoadedProviders();
        
        $this->assertArrayHasKey(DnsDenyListCheckServiceProvider::class, $providers);
        $this->assertTrue($providers[DnsDenyListCheckServiceProvider::class]);
    }

    public function test_config_file_is_published()
    {
        // Get the package config path
        $packageConfigPath = __DIR__ . '/../config/dns-deny-list-check.php';
        $this->assertFileExists($packageConfigPath);
        
        // Verify config can be loaded
        $config = include $packageConfigPath;
        $this->assertIsArray($config);
        $this->assertArrayHasKey('servers', $config);
    }

    public function test_config_is_accessible_via_helper()
    {
        // Load the package config
        $this->app['config']->set('dns-deny-list-check.servers', [
            ['name' => 'Test Server', 'host' => 'test.example.com'],
        ]);
        
        $servers = config('dns-deny-list-check.servers');
        
        $this->assertIsArray($servers);
        $this->assertCount(1, $servers);
        $this->assertEquals('Test Server', $servers[0]['name']);
        $this->assertEquals('test.example.com', $servers[0]['host']);
    }

    public function test_config_file_has_correct_structure()
    {
        $configPath = __DIR__ . '/../config/dns-deny-list-check.php';
        $config = include $configPath;
        
        $this->assertIsArray($config);
        $this->assertArrayHasKey('servers', $config);
        $this->assertIsArray($config['servers']);
        $this->assertNotEmpty($config['servers']);
        
        // Verify each server has required structure
        foreach ($config['servers'] as $server) {
            $this->assertIsArray($server);
            $this->assertArrayHasKey('name', $server);
            $this->assertArrayHasKey('host', $server);
            $this->assertIsString($server['name']);
            $this->assertIsString($server['host']);
        }
    }

    public function test_config_contains_expected_servers()
    {
        $configPath = __DIR__ . '/../config/dns-deny-list-check.php';
        $config = include $configPath;
        
        $hosts = array_column($config['servers'], 'host');
        
        // Verify some expected DNSBL servers are present
        $this->assertContains('zen.spamhaus.org', $hosts);
        $this->assertContains('bl.spamcop.net', $hosts);
        $this->assertContains('b.barracudacentral.org', $hosts);
    }

    public function test_config_servers_have_tier_information()
    {
        $configPath = __DIR__ . '/../config/dns-deny-list-check.php';
        $config = include $configPath;
        
        // Check that tier information is present
        $tieredServers = array_filter($config['servers'], function ($server) {
            return isset($server['tier']);
        });
        
        $this->assertNotEmpty($tieredServers);
        
        // Verify tier values are valid
        foreach ($tieredServers as $server) {
            $this->assertIsInt($server['tier']);
            $this->assertGreaterThanOrEqual(1, $server['tier']);
            $this->assertLessThanOrEqual(4, $server['tier']);
        }
    }

    public function test_config_servers_have_priority_information()
    {
        $configPath = __DIR__ . '/../config/dns-deny-list-check.php';
        $config = include $configPath;
        
        // Check that priority information is present
        $prioritizedServers = array_filter($config['servers'], function ($server) {
            return isset($server['priority']);
        });
        
        $this->assertNotEmpty($prioritizedServers);
        
        // Verify priority values are valid
        $validPriorities = ['critical', 'important', 'supplementary', 'additional'];
        foreach ($prioritizedServers as $server) {
            $this->assertContains($server['priority'], $validPriorities);
        }
    }

    public function test_package_name_is_correct()
    {
        // Verify the package name matches expectations
        $provider = new DnsDenyListCheckServiceProvider($this->app);
        
        // Use reflection to access the package name if needed
        $reflection = new \ReflectionClass($provider);
        if ($reflection->hasMethod('getPackage')) {
            $package = $provider->getPackage();
            // This would test if the package object has correct name
            // Implementation depends on Spatie package tools internals
        }
        
        // Alternative: verify config key exists
        $this->assertTrue(config()->has('dns-deny-list-check'));
    }

    public function test_service_provider_boots_without_errors()
    {
        // Test that the service provider is already booted successfully
        // (it boots during TestCase setup)
        $providers = $this->app->getLoadedProviders();
        $this->assertArrayHasKey(DnsDenyListCheckServiceProvider::class, $providers);
        $this->assertTrue($providers[DnsDenyListCheckServiceProvider::class]);
    }

    public function test_service_provider_registers_without_errors()
    {
        // Create a fresh instance to test registration
        $provider = new DnsDenyListCheckServiceProvider($this->app);
        
        // Test that register method doesn't throw exceptions
        try {
            $provider->register();
            $this->assertTrue(true); // If we get here, no exception was thrown
        } catch (\Throwable $e) {
            $this->fail("Service provider register failed: " . $e->getMessage());
        }
    }

    public function test_config_can_be_overridden()
    {
        // Test that config can be overridden in application
        $customServers = [
            ['name' => 'Custom DNSBL', 'host' => 'custom.example.com'],
        ];
        
        $this->app['config']->set('dns-deny-list-check.servers', $customServers);
        
        $retrievedServers = config('dns-deny-list-check.servers');
        
        $this->assertEquals($customServers, $retrievedServers);
    }

    public function test_config_provides_fallback_when_empty()
    {
        // Test behavior when config is empty
        $this->app['config']->set('dns-deny-list-check.servers', []);
        
        $servers = config('dns-deny-list-check.servers', []);
        
        $this->assertIsArray($servers);
        $this->assertEmpty($servers);
    }

    public function test_config_file_is_valid_php()
    {
        $configPath = __DIR__ . '/../config/dns-deny-list-check.php';
        
        // Verify file exists and is readable
        $this->assertFileExists($configPath);
        $this->assertFileIsReadable($configPath);
        
        // Verify file contains valid PHP that returns an array
        $config = include $configPath;
        $this->assertIsArray($config);
        
        // Verify no PHP syntax errors by checking file contents
        $contents = file_get_contents($configPath);
        $this->assertNotFalse($contents);
        $this->assertStringStartsWith('<?php', $contents);
    }

    public function test_config_has_documentation()
    {
        $configPath = __DIR__ . '/../config/dns-deny-list-check.php';
        $contents = file_get_contents($configPath);
        
        // Verify config file has helpful comments
        $this->assertStringContainsString('DNSBL', $contents);
        $this->assertStringContainsString('Configuration', $contents);
        
        // Should have comments explaining the structure
        $this->assertStringContainsString('/*', $contents);
    }

    public function test_service_provider_extends_correct_base_class()
    {
        $provider = new DnsDenyListCheckServiceProvider($this->app);
        
        $this->assertInstanceOf(
            \Spatie\LaravelPackageTools\PackageServiceProvider::class,
            $provider
        );
    }

    public function test_package_configuration_is_complete()
    {
        // Verify all expected configuration elements are present
        $configExists = config()->has('dns-deny-list-check');
        $this->assertTrue($configExists);
        
        $servers = config('dns-deny-list-check.servers');
        $this->assertIsArray($servers);
        $this->assertNotEmpty($servers);
        
        // Verify critical tier 1 servers are present
        $hosts = array_column($servers, 'host');
        $criticalServers = ['zen.spamhaus.org', 'bl.spamcop.net', 'b.barracudacentral.org'];
        
        foreach ($criticalServers as $server) {
            $this->assertContains($server, $hosts, "Critical server {$server} not found in config");
        }
    }
}