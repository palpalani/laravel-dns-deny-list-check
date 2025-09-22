<?php

declare(strict_types=1);

namespace palPalani\DnsDenyListCheck;

class DnsDenyListCheck
{
    /** @var array<int, array{name: string, host: string}> */
    private array $dnsblServers;

    public function __construct(?array $dnsblServers = null)
    {
        $this->dnsblServers = $dnsblServers ?? config('dns-deny-list-check.servers') ?? [];
    }

    public function check(string $ip): array
    {

        $result = [];

        if (! filter_var($ip, FILTER_VALIDATE_IP)) {
            return [
                'success' => false,
                'message' => 'Invalid IP address',
                'data' => null,
            ];
        }

        $reverseIp = \implode('.', \array_reverse(\explode('.', $ip)));

        foreach ($this->dnsblServers as $server) {
            if (!isset($server['host']) || !is_string($server['host'])) {
                continue;
            }
            
            $host = $server['host'];
            try {
                $dnsr = $reverseIp . '.' . $host . '.';
                if (\checkdnsrr($dnsr, 'A')) {
                    $listed = true;
                } else {
                    $listed = false;
                }

                $result[] = [
                    'host' => $host,
                    'listed' => $listed,
                ];
            } catch (\Throwable $exception) {
                $result[] = [
                    'host' => $host,
                    'listed' => 'Unknown',
                    'message' => $exception->getMessage(),
                ];
            }
        }

        return [
            'success' => true,
            'message' => '',
            'data' => $result,
        ];
    }
}
