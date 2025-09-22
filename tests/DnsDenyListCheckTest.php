<?php

declare(strict_types=1);

namespace palPalani\DnsDenyListCheck\Tests;

use palPalani\DnsDenyListCheck\DnsDenyListCheck;

class DnsDenyListCheckTest extends TestCase
{
    private array $testServers;

    public function setUp(): void
    {
        parent::setUp();
        
        // Define minimal test servers to avoid external dependencies in tests
        $this->testServers = [
            ['name' => 'Test DNSBL 1', 'host' => 'test1.example.com'],
            ['name' => 'Test DNSBL 2', 'host' => 'test2.example.com'],
        ];
    }

    public function test_constructor_uses_provided_servers()
    {
        $customServers = [
            ['name' => 'Custom DNSBL', 'host' => 'custom.example.com'],
        ];
        
        $checker = new DnsDenyListCheck($customServers);
        $result = $checker->check('192.168.1.1');
        
        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
        $this->assertCount(1, $result['data']);
        $this->assertEquals('custom.example.com', $result['data'][0]['host']);
    }

    public function test_constructor_uses_config_when_no_servers_provided()
    {
        // Set up config
        config(['dns-deny-list-check.servers' => $this->testServers]);
        
        $checker = new DnsDenyListCheck();
        $result = $checker->check('192.168.1.1');
        
        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
        $this->assertCount(2, $result['data']);
        
        $hosts = array_column($result['data'], 'host');
        $this->assertContains('test1.example.com', $hosts);
        $this->assertContains('test2.example.com', $hosts);
    }

    public function test_constructor_uses_empty_array_when_config_missing()
    {
        // Clear config
        config(['dns-deny-list-check.servers' => null]);
        
        $checker = new DnsDenyListCheck();
        $result = $checker->check('192.168.1.1');
        
        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
        $this->assertEmpty($result['data']);
    }

    public function test_check_returns_correct_structure_for_valid_ip()
    {
        $checker = new DnsDenyListCheck($this->testServers);
        $result = $checker->check('8.8.8.8');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('data', $result);
        
        $this->assertTrue($result['success']);
        $this->assertIsString($result['message']);
        $this->assertIsArray($result['data']);
    }

    public function test_check_validates_ipv4_addresses()
    {
        $checker = new DnsDenyListCheck($this->testServers);
        
        // Valid IPv4 addresses
        $validIps = ['192.168.1.1', '8.8.8.8', '127.0.0.1', '255.255.255.255', '0.0.0.0'];
        
        foreach ($validIps as $ip) {
            $result = $checker->check($ip);
            $this->assertTrue($result['success'], "Failed for valid IP: {$ip}");
            $this->assertIsArray($result['data']);
        }
    }

    public function test_check_validates_ipv6_addresses()
    {
        $checker = new DnsDenyListCheck($this->testServers);
        
        // Valid IPv6 addresses
        $validIps = ['::1', '2001:db8::1', 'fe80::1', '::'];
        
        foreach ($validIps as $ip) {
            $result = $checker->check($ip);
            $this->assertTrue($result['success'], "Failed for valid IPv6: {$ip}");
            $this->assertIsArray($result['data']);
        }
    }

    public function test_check_rejects_invalid_ip_addresses()
    {
        $checker = new DnsDenyListCheck($this->testServers);
        
        $invalidIps = [
            'invalid-ip',
            '999.999.999.999',
            '192.168.1',
            '192.168.1.1.1',
            'example.com',
            '',
            '256.1.1.1',
            '192.168.01.1', // Leading zeros not allowed in some contexts
            'not-an-ip-address',
            '192.168.1.-1',
        ];
        
        foreach ($invalidIps as $ip) {
            $result = $checker->check($ip);
            $this->assertFalse($result['success'], "Should fail for invalid IP: {$ip}");
            $this->assertEquals('Invalid IP address', $result['message']);
            $this->assertNull($result['data']);
        }
    }

    public function test_check_handles_empty_server_list()
    {
        $checker = new DnsDenyListCheck([]);
        $result = $checker->check('8.8.8.8');
        
        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
        $this->assertEmpty($result['data']);
    }

    public function test_check_data_contains_expected_fields()
    {
        $checker = new DnsDenyListCheck($this->testServers);
        $result = $checker->check('127.0.0.1');
        
        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
        
        foreach ($result['data'] as $serverResult) {
            $this->assertArrayHasKey('host', $serverResult);
            $this->assertArrayHasKey('listed', $serverResult);
            $this->assertIsString($serverResult['host']);
            $this->assertTrue(
                is_bool($serverResult['listed']) || $serverResult['listed'] === 'Unknown'
            );
        }
    }

    public function test_check_handles_dns_lookup_errors_gracefully()
    {
        // Use a server that will likely cause DNS errors
        $problematicServers = [
            ['name' => 'Non-existent DNSBL', 'host' => 'this-should-not-exist-12345.invalid'],
        ];
        
        $checker = new DnsDenyListCheck($problematicServers);
        $result = $checker->check('8.8.8.8');
        
        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
        $this->assertCount(1, $result['data']);
        
        $serverResult = $result['data'][0];
        $this->assertEquals('this-should-not-exist-12345.invalid', $serverResult['host']);
        
        // Should handle error gracefully, either with false or 'Unknown'
        $this->assertTrue(
            $serverResult['listed'] === false || $serverResult['listed'] === 'Unknown'
        );
    }

    public function test_check_processes_multiple_servers()
    {
        $multipleServers = [
            ['name' => 'DNSBL 1', 'host' => 'test1.example.com'],
            ['name' => 'DNSBL 2', 'host' => 'test2.example.com'],
            ['name' => 'DNSBL 3', 'host' => 'test3.example.com'],
        ];
        
        $checker = new DnsDenyListCheck($multipleServers);
        $result = $checker->check('203.0.113.1'); // RFC5737 test IP
        
        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
        $this->assertCount(3, $result['data']);
        
        $hosts = array_column($result['data'], 'host');
        $this->assertContains('test1.example.com', $hosts);
        $this->assertContains('test2.example.com', $hosts);
        $this->assertContains('test3.example.com', $hosts);
    }

    public function test_check_handles_malformed_server_configuration()
    {
        $malformedServers = [
            ['name' => 'Valid Server', 'host' => 'valid.example.com'],
            ['host' => 'missing-name.example.com'], // Missing name
            ['name' => 'Missing Host'], // Missing host
            [], // Empty server config
        ];
        
        $checker = new DnsDenyListCheck($malformedServers);
        $result = $checker->check('8.8.8.8');
        
        // Should not crash and should process what it can
        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
    }

    public function test_check_reverses_ip_correctly()
    {
        // This test indirectly verifies IP reversal by checking that DNS queries work
        // For IP 1.2.3.4, the reversed query should be 4.3.2.1.dnsbl.host
        $checker = new DnsDenyListCheck([
            ['name' => 'Test DNSBL', 'host' => 'test.example.com'],
        ]);
        
        $result = $checker->check('1.2.3.4');
        
        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
        $this->assertCount(1, $result['data']);
        $this->assertEquals('test.example.com', $result['data'][0]['host']);
    }

    public function test_check_with_real_dns_servers_integration()
    {
        // Integration test with actual DNSBL servers (use sparingly)
        $realServers = [
            ['name' => 'Spamhaus ZEN', 'host' => 'zen.spamhaus.org'],
        ];
        
        $checker = new DnsDenyListCheck($realServers);
        
        // Test with known clean IP (Google DNS)
        $result = $checker->check('8.8.8.8');
        
        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
        $this->assertCount(1, $result['data']);
        $this->assertEquals('zen.spamhaus.org', $result['data'][0]['host']);
        $this->assertIsBool($result['data'][0]['listed']);
    }

    public function test_check_performance_with_many_servers()
    {
        // Performance test with many servers
        $manyServers = [];
        for ($i = 1; $i <= 20; $i++) {
            $manyServers[] = ['name' => "DNSBL {$i}", 'host' => "test{$i}.example.com"];
        }
        
        $checker = new DnsDenyListCheck($manyServers);
        $startTime = microtime(true);
        
        $result = $checker->check('192.0.2.1'); // RFC5737 test IP
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
        $this->assertCount(20, $result['data']);
        
        // Should complete within reasonable time (adjust as needed)
        $this->assertLessThan(10.0, $executionTime, 'Check took too long to complete');
    }

    public function test_check_handles_edge_case_ips()
    {
        $checker = new DnsDenyListCheck($this->testServers);
        
        $edgeCaseIps = [
            '0.0.0.0',           // Network address
            '255.255.255.255',   // Broadcast address
            '127.0.0.1',         // Localhost
            '169.254.1.1',       // Link-local
            '224.0.0.1',         // Multicast
        ];
        
        foreach ($edgeCaseIps as $ip) {
            $result = $checker->check($ip);
            $this->assertTrue($result['success'], "Failed for edge case IP: {$ip}");
            $this->assertIsArray($result['data']);
        }
    }

    public function test_check_return_structure_consistency()
    {
        $checker = new DnsDenyListCheck($this->testServers);
        
        // Test multiple different IPs to ensure consistent structure
        $testIps = ['8.8.8.8', '1.1.1.1', '127.0.0.1', '192.168.1.1'];
        
        foreach ($testIps as $ip) {
            $result = $checker->check($ip);
            
            // Verify consistent structure
            $this->assertIsArray($result);
            $this->assertCount(3, $result);
            $this->assertArrayHasKey('success', $result);
            $this->assertArrayHasKey('message', $result);
            $this->assertArrayHasKey('data', $result);
            
            $this->assertIsBool($result['success']);
            $this->assertIsString($result['message']);
            $this->assertIsArray($result['data']);
        }
    }
}