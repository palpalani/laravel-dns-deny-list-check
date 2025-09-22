<?php

declare(strict_types=1);

namespace palPalani\DnsDenyListCheck;

use InvalidArgumentException;

/**
 * Enhanced DNS Deny List (DNSBL/RBL) Checker
 *
 * Checks IP addresses against DNS-based email blacklists with comprehensive
 * error handling, IPv6 support, performance optimizations, and detailed reporting.
 *
 * @author palPalani
 *
 * @version 2.0.0
 */
class DnsDenyListCheck
{
    /** @var array<int, array{name: string, host: string, tier?: int, priority?: string}> */
    private readonly array $dnsblServers;

    private readonly int $timeoutSeconds;

    private readonly bool $ipv6Enabled;

    private readonly bool $concurrentEnabled;

    /**
     * @param  array<int, array{name: string, host: string, tier?: int, priority?: string}>|null  $dnsblServers
     * @param  int  $timeoutSeconds  DNS query timeout in seconds
     * @param  bool  $ipv6Enabled  Enable IPv6 support
     * @param  bool  $concurrentEnabled  Enable concurrent DNS queries (future enhancement)
     */
    public function __construct(
        ?array $dnsblServers = null,
        int $timeoutSeconds = 10,
        bool $ipv6Enabled = true,
        bool $concurrentEnabled = false
    ) {
        $this->dnsblServers = $this->validateAndSanitizeServers(
            $dnsblServers ?? config('dns-deny-list-check.servers', [])
        );
        $this->timeoutSeconds = max(1, min(30, $timeoutSeconds)); // Clamp between 1-30 seconds
        $this->ipv6Enabled = $ipv6Enabled;
        $this->concurrentEnabled = $concurrentEnabled;
    }

    /**
     * Check IP address against all configured DNSBL servers
     *
     * @param  string  $ip  IPv4 or IPv6 address to check
     * @return array{success: bool, message: string, data: array|null, stats?: array}
     *
     * @throws InvalidArgumentException for invalid IP addresses
     */
    public function check(string $ip): array
    {
        $startTime = microtime(true);

        try {
            // Comprehensive IP validation
            $validatedIp = $this->validateIpAddress($ip);
            $ipVersion = $this->getIpVersion($validatedIp);

            // Check if IPv6 is supported
            if ($ipVersion === 6 && ! $this->ipv6Enabled) {
                return $this->buildErrorResponse('IPv6 addresses are not supported in current configuration');
            }

            // Validate servers are configured
            if (empty($this->dnsblServers)) {
                return $this->buildErrorResponse('No DNSBL servers configured');
            }

            // Perform DNSBL checks
            $results = $this->performDnsblChecks($validatedIp, $ipVersion);

            // Calculate statistics
            $stats = $this->calculateStats($results, microtime(true) - $startTime);

            return [
                'success' => true,
                'message' => $this->generateSummaryMessage($stats),
                'data' => $results,
                'stats' => $stats,
                'ip_version' => $ipVersion,
                'checked_at' => now()->toISOString(),
            ];

        } catch (InvalidArgumentException $e) {
            return $this->buildErrorResponse($e->getMessage());
        } catch (\Throwable $e) {
            return $this->buildErrorResponse('Unexpected error: '.$e->getMessage());
        }
    }

    /**
     * Get detailed information about configured DNSBL servers
     *
     * @return array{total: int, by_tier: array, by_priority: array, servers: array}
     */
    public function getServerInfo(): array
    {
        $byTier = [];
        $byPriority = [];

        foreach ($this->dnsblServers as $server) {
            $tier = $server['tier'] ?? 'unknown';
            $priority = $server['priority'] ?? 'unknown';

            $byTier[$tier] = ($byTier[$tier] ?? 0) + 1;
            $byPriority[$priority] = ($byPriority[$priority] ?? 0) + 1;
        }

        return [
            'total' => count($this->dnsblServers),
            'by_tier' => $byTier,
            'by_priority' => $byPriority,
            'ipv6_enabled' => $this->ipv6Enabled,
            'timeout_seconds' => $this->timeoutSeconds,
            'servers' => $this->dnsblServers,
        ];
    }

    /**
     * Validate and sanitize DNSBL server configuration
     *
     * @return array<int, array{name: string, host: string, tier?: int, priority?: string}>
     */
    private function validateAndSanitizeServers(array $servers): array
    {
        $validated = [];

        foreach ($servers as $server) {
            if (! is_array($server)) {
                continue; // Skip invalid entries
            }

            if (! isset($server['host']) || ! is_string($server['host']) || empty(trim($server['host']))) {
                continue; // Skip servers without valid host
            }

            $host = trim($server['host']);

            // Validate hostname format
            if (! $this->isValidHostname($host)) {
                continue; // Skip invalid hostnames
            }

            $validated[] = [
                'name' => trim($server['name'] ?? $host),
                'host' => $host,
                'tier' => is_int($server['tier'] ?? null) ? $server['tier'] : null,
                'priority' => is_string($server['priority'] ?? null) ? $server['priority'] : null,
            ];
        }

        return $validated;
    }

    /**
     * Validate IP address with comprehensive checks
     *
     * @return string Validated and normalized IP address
     *
     * @throws InvalidArgumentException
     */
    private function validateIpAddress(string $ip): string
    {
        $ip = trim($ip);

        if (empty($ip)) {
            throw new InvalidArgumentException('IP address cannot be empty');
        }

        // Check for common invalid patterns
        if (preg_match('/[^0-9a-fA-F:.\/]/', $ip)) {
            throw new InvalidArgumentException('IP address contains invalid characters');
        }

        // Remove CIDR notation if present
        if (str_contains($ip, '/')) {
            $ip = explode('/', $ip)[0];
        }

        // Validate IPv4/IPv6
        $validatedIp = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        if ($validatedIp === false) {
            // Try without private/reserved range restrictions for testing purposes
            $validatedIp = filter_var($ip, FILTER_VALIDATE_IP);
            if ($validatedIp === false) {
                throw new InvalidArgumentException("Invalid IP address format: {$ip}");
            }
        }

        return $validatedIp;
    }

    /**
     * Get IP version (4 or 6)
     */
    private function getIpVersion(string $ip): int
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return 4;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return 6;
        }

        throw new InvalidArgumentException('Unable to determine IP version');
    }

    /**
     * Perform DNSBL checks against all configured servers
     *
     * @return array<int, array{name: string, host: string, listed: bool|string, response_time?: float, message?: string, tier?: int, priority?: string}>
     */
    private function performDnsblChecks(string $ip, int $ipVersion): array
    {
        $results = [];

        foreach ($this->dnsblServers as $server) {
            $results[] = $this->checkSingleDnsbl($ip, $ipVersion, $server);
        }

        return $results;
    }

    /**
     * Check single DNSBL server
     *
     * @param  array{name: string, host: string, tier?: int, priority?: string}  $server
     * @return array{name: string, host: string, listed: bool|string, response_time?: float, message?: string, tier?: int, priority?: string}
     */
    private function checkSingleDnsbl(string $ip, int $ipVersion, array $server): array
    {
        $startTime = microtime(true);
        $result = [
            'name' => $server['name'],
            'host' => $server['host'],
            'tier' => $server['tier'] ?? null,
            'priority' => $server['priority'] ?? null,
        ];

        try {
            $reverseIp = $this->buildReverseIp($ip, $ipVersion);
            $queryHost = $reverseIp.'.'.$server['host'].'.';

            // Perform DNS query with timeout handling
            $listed = $this->performDnsQuery($queryHost);

            $result['listed'] = $listed;
            $result['response_time'] = round((microtime(true) - $startTime) * 1000, 2); // milliseconds

        } catch (\Throwable $exception) {
            $result['listed'] = 'Unknown';
            $result['message'] = $exception->getMessage();
            $result['response_time'] = round((microtime(true) - $startTime) * 1000, 2);
        }

        return $result;
    }

    /**
     * Build reverse IP notation for DNSBL query
     */
    private function buildReverseIp(string $ip, int $ipVersion): string
    {
        if ($ipVersion === 4) {
            return implode('.', array_reverse(explode('.', $ip)));
        }

        if ($ipVersion === 6) {
            // IPv6 reverse notation (simplified for common DNSBLs)
            $expandedIp = $this->expandIpv6($ip);
            $chars = str_replace(':', '', $expandedIp);

            return implode('.', array_reverse(str_split($chars)));
        }

        throw new InvalidArgumentException('Unsupported IP version');
    }

    /**
     * Expand IPv6 address to full notation
     */
    private function expandIpv6(string $ipv6): string
    {
        $hex = unpack('H*hex', inet_pton($ipv6));

        return substr(preg_replace('/([A-f0-9]{4})/', '$1:', $hex['hex']), 0, -1);
    }

    /**
     * Perform DNS query with error handling
     */
    private function performDnsQuery(string $queryHost): bool
    {
        // Set timeout (Note: checkdnsrr doesn't support timeout directly)
        $originalTimeout = ini_get('default_socket_timeout');
        ini_set('default_socket_timeout', (string) $this->timeoutSeconds);

        try {
            $result = checkdnsrr($queryHost, 'A');

            return $result;
        } finally {
            // Restore original timeout
            ini_set('default_socket_timeout', $originalTimeout);
        }
    }

    /**
     * Calculate statistics from results
     *
     * @return array{total_servers: int, listed_count: int, clean_count: int, unknown_count: int, total_time: float, avg_response_time: float, listing_percentage: float}
     */
    private function calculateStats(array $results, float $totalTime): array
    {
        $totalServers = count($results);
        $listedCount = 0;
        $cleanCount = 0;
        $unknownCount = 0;
        $totalResponseTime = 0;
        $responseTimeCount = 0;

        foreach ($results as $result) {
            if ($result['listed'] === true) {
                $listedCount++;
            } elseif ($result['listed'] === false) {
                $cleanCount++;
            } else {
                $unknownCount++;
            }

            if (isset($result['response_time'])) {
                $totalResponseTime += $result['response_time'];
                $responseTimeCount++;
            }
        }

        return [
            'total_servers' => $totalServers,
            'listed_count' => $listedCount,
            'clean_count' => $cleanCount,
            'unknown_count' => $unknownCount,
            'total_time' => round($totalTime * 1000, 2), // milliseconds
            'avg_response_time' => $responseTimeCount > 0 ? round($totalResponseTime / $responseTimeCount, 2) : 0,
            'listing_percentage' => $totalServers > 0 ? round(($listedCount / $totalServers) * 100, 1) : 0,
        ];
    }

    /**
     * Generate human-readable summary message
     */
    private function generateSummaryMessage(array $stats): string
    {
        if ($stats['listed_count'] === 0) {
            return "IP is clean - not listed on any of {$stats['total_servers']} DNSBL servers";
        }

        return "IP is listed on {$stats['listed_count']} out of {$stats['total_servers']} DNSBL servers ({$stats['listing_percentage']}%)";
    }

    /**
     * Build error response
     *
     * @return array{success: bool, message: string, data: null}
     */
    private function buildErrorResponse(string $message): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => null,
        ];
    }

    /**
     * Validate hostname format
     */
    private function isValidHostname(string $hostname): bool
    {
        // Basic hostname validation
        return (bool) preg_match('/^[a-zA-Z0-9]([a-zA-Z0-9\-\.]*[a-zA-Z0-9])?$/', $hostname)
               && strlen($hostname) <= 253
               && ! str_starts_with($hostname, '.')
               && ! str_ends_with($hostname, '.');
    }
}
