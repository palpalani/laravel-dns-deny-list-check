# Laravel DNS Deny List Check

[![Latest Version on Packagist](https://img.shields.io/packagist/v/palpalani/laravel-dns-deny-list-check.svg?style=flat-square)](https://packagist.org/packages/palpalani/laravel-dns-deny-list-check)
[![Total Downloads](https://img.shields.io/packagist/dt/palpalani/laravel-dns-deny-list-check.svg?style=flat-square)](https://packagist.org/packages/palpalani/laravel-dns-deny-list-check)
[![License](https://img.shields.io/packagist/l/palpalani/laravel-dns-deny-list-check.svg?style=flat-square)](https://packagist.org/packages/palpalani/laravel-dns-deny-list-check)
[![PHP Version](https://img.shields.io/packagist/php-v/palpalani/laravel-dns-deny-list-check.svg?style=flat-square)](https://packagist.org/packages/palpalani/laravel-dns-deny-list-check)
[![Laravel Version](https://img.shields.io/badge/Laravel-11.x%20%7C%2012.x-red.svg?style=flat-square)](https://laravel.com)

[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/palpalani/laravel-dns-deny-list-check/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/palpalani/laravel-dns-deny-list-check/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub PHPStan Action Status](https://img.shields.io/github/actions/workflow/status/palpalani/laravel-dns-deny-list-check/analyse.yml?branch=main&label=phpstan&style=flat-square)](https://github.com/palpalani/laravel-dns-deny-list-check/actions?query=workflow%3Aanalyse+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/palpalani/laravel-dns-deny-list-check/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/palpalani/laravel-dns-deny-list-check/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![GitHub Check Links Action Status](https://img.shields.io/github/actions/workflow/status/palpalani/laravel-dns-deny-list-check/check-links.yml?branch=main&label=link%20check&style=flat-square)](https://github.com/palpalani/laravel-dns-deny-list-check/actions?query=workflow%3Acheck-links+branch%3Amain)

A modern, production-ready Laravel package for checking email server IP addresses against **verified DNS-based blacklists (DNSBL/RBL)**. This package helps ensure email deliverability by testing your mail server against 12 carefully curated, actively maintained blacklist services.

## 📑 Table of Contents

- [Features](#-features)
- [Requirements](#-requirements)
- [Architecture](#️-architecture)
- [Installation](#-installation)
- [Configuration](#configuration-optional)
- [Usage](#-usage)
- [Testing](#-testing)
- [Production Considerations](#-production-considerations)
- [Documentation](#-documentation)
- [Tools & Standards](#-tools--standards)
- [Quality Metrics](#-quality-metrics)
- [Contributing](#-contributing)
- [Security](#-security-vulnerabilities)
- [License](#-license)

## ✨ Features

- 🔍 **Production-Verified DNSBL Servers** - Only 12 verified, functional servers (January 2025)
- 🚀 **Modern PHP 8.3+** - Uses readonly properties, constructor promotion, and strict typing
- 🌐 **Full IPv4 & IPv6 Support** - Handles both IP versions with proper reverse notation
- ⚡ **Performance Optimized** - Configurable timeouts and optional concurrent checking
- 📊 **Detailed Statistics** - Comprehensive response data with performance metrics
- 🎯 **Tier-Based Checking** - Critical, Important, and Supplementary DNSBL categories
- 🔧 **Laravel Integration** - Service provider, facade, and configuration support
- ✅ **Comprehensive Testing** - 47 tests with 374 assertions using Pest
- 🛡️ **Edge Case Handling** - Graceful error handling and validation
- 🔒 **Production Ready** - Zero false positives, reliable DNSBL servers only
- 📈 **Type Safe** - Full PHPStan static analysis coverage

## 📋 Requirements

- PHP **8.3** or higher
- Laravel **11.x** or **12.x**
- ext-dns (for DNS lookups)

## 🏗️ Architecture

Built with modern PHP and Laravel best practices:
- **Type Safety**: Full type hints, strict types, and PHPStan analysis
- **Testing**: Comprehensive Pest test suite with high coverage
- **Code Quality**: Laravel Pint for code formatting, PHPStan for static analysis
- **Performance**: Optimized DNS queries with configurable timeouts

## 🚀 Installation

Install the package via Composer:

```bash
composer require palpalani/laravel-dns-deny-list-check
```

The service provider will be automatically registered thanks to Laravel's package auto-discovery.

### Configuration (Optional)

Publish the configuration file to customize DNSBL servers:

```bash
php artisan vendor:publish --provider="palPalani\DnsDenyListCheck\DnsDenyListCheckServiceProvider" --tag="laravel-dns-deny-list-check-config"
```

This creates `config/dns-deny-list-check.php` with 12 production-verified DNSBL servers:

```php
return [
    'servers' => [
        // TIER 1: CRITICAL - Most trusted DNSBLs
        ['name' => 'SpamCop Blocking List', 'host' => 'bl.spamcop.net', 'tier' => 1, 'priority' => 'critical'],
        ['name' => 'Barracuda Reputation Block List', 'host' => 'b.barracudacentral.org', 'tier' => 1, 'priority' => 'critical'],
        ['name' => 'UCEPROTECT Level 1', 'host' => 'dnsbl-1.uceprotect.net', 'tier' => 1, 'priority' => 'critical'],
        
        // TIER 2: IMPORTANT - Specialized authority DNSBLs
        ['name' => 'DroneB Anti-Abuse', 'host' => 'dnsbl.dronebl.org', 'tier' => 2, 'priority' => 'important'],
        ['name' => 'Backscatterer IPS', 'host' => 'ips.backscatterer.org', 'tier' => 2, 'priority' => 'important'],
        // ... additional servers
    ],
];
```

## 📖 Usage

### Basic Usage

```php
use palPalani\DnsDenyListCheck\DnsDenyListCheck;

// Using the class directly
$checker = new DnsDenyListCheck();
$result = $checker->check('8.8.8.8');

// Using the facade
use palPalani\DnsDenyListCheck\DnsDenyListCheckFacade as DnsDenyListCheck;

$result = DnsDenyListCheck::check('8.8.8.8');
```

### Response Structure

```php
[
    'success' => true,
    'message' => 'IP check completed: 0 blacklists found IP as listed out of 12 total servers checked.',
    'data' => [
        [
            'host' => 'bl.spamcop.net',
            'listed' => false,
            'response_time' => 0.123,
            'tier' => 1,
            'priority' => 'critical'
        ],
        // ... more results
    ],
    'stats' => [
        'total_checked' => 12,
        'total_listed' => 0,
        'total_unlisted' => 12,
        'total_errors' => 0,
        'average_response_time' => 0.098
    ],
    'ip_version' => 'IPv4',
    'checked_at' => '2025-01-22T10:30:45.123456Z'
]
```

### Advanced Configuration

```php
use palPalani\DnsDenyListCheck\DnsDenyListCheck;

// Custom server list
$customServers = [
    ['name' => 'Custom DNSBL', 'host' => 'custom.dnsbl.com', 'tier' => 1, 'priority' => 'critical']
];

$checker = new DnsDenyListCheck(
    dnsblServers: $customServers,
    timeoutSeconds: 15,
    ipv6Enabled: true,
    concurrentEnabled: false
);

$result = $checker->check('2001:db8::1'); // IPv6 support
```

### Laravel Service Container

```php
// In a service provider
$this->app->bind(DnsDenyListCheck::class, function ($app) {
    return new DnsDenyListCheck(
        timeoutSeconds: config('dns-deny-list-check.timeout', 10)
    );
});

// In a controller
public function checkIp(DnsDenyListCheck $checker, Request $request)
{
    $result = $checker->check($request->ip());
    
    return response()->json($result);
}
```

## 🧪 Testing

Run the comprehensive test suite:

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage
```

The package includes **47 tests with 374 assertions** using [Pest](https://pestphp.com) covering:

- ✅ IPv4 and IPv6 validation
- ✅ DNSBL server connectivity
- ✅ Error handling and edge cases
- ✅ Facade functionality
- ✅ Performance testing
- ✅ Configuration validation
- ✅ Type safety and static analysis

### Code Quality

```bash
# Run PHPStan static analysis
composer analyse

# Fix code style with Laravel Pint
composer format
```

## 🔒 Production Considerations

### DNSBL Server Reliability

This package uses **only verified, production-ready DNSBL servers** (January 2025):

- ❌ **Removed non-functional services**: SORBS (shut down June 2024), SpamRats, defunct Spamhaus services
- ✅ **12 verified servers**: Tested for DNS resolution, active maintenance, and low false positives
- 🎯 **Tier-based approach**: Critical (3), Important (4), Supplementary (5) categories

### Performance Tips

```php
// For high-volume applications
$checker = new DnsDenyListCheck(
    timeoutSeconds: 5,        // Reduce timeout for faster responses
    concurrentEnabled: true   // Enable concurrent checking (when available)
);

// Cache results to avoid repeated checks
Cache::remember("dnsbl_check_{$ip}", 3600, function () use ($ip, $checker) {
    return $checker->check($ip);
});
```

### Error Handling

```php
$result = DnsDenyListCheck::check($ip);

if (!$result['success']) {
    Log::warning('DNSBL check failed', [
        'ip' => $ip,
        'error' => $result['message']
    ]);
    
    // Fallback logic
    return ['status' => 'unknown', 'reason' => 'DNSBL service unavailable'];
}

// Check if IP is blacklisted
$blacklisted = $result['stats']['total_listed'] > 0;
```

## 📚 Documentation

Comprehensive documentation is available in the repository:

- [API Reference](docs/api.md)
- [Configuration Guide](docs/configuration.md)
- [Performance Optimization](docs/performance.md)
- [DNSBL Server Guide](docs/dnsbl-servers.md)

## 🔧 Tools & Standards

This package uses modern development tools and follows best practices:

- **Testing**: [Pest PHP](https://pestphp.com) - A delightful PHP testing framework
- **Code Style**: [Laravel Pint](https://laravel.com/docs/pint) - Opinionated PHP code style fixer
- **Static Analysis**: [PHPStan](https://phpstan.org) with [Larastan](https://github.com/larastan/larastan)
- **CI/CD**: GitHub Actions with comprehensive test matrix
- **Code Quality**: Automated code style fixes and static analysis on every push

## 📊 Quality Metrics

- ✅ **100% Type Coverage** - Full type hints and PHPStan analysis
- ✅ **47 Tests** - Comprehensive test suite with Pest
- ✅ **374 Assertions** - Thorough validation of all features
- ✅ **Zero Dependencies** - No external HTTP dependencies, uses native DNS resolution
- ✅ **Production Verified** - Only active, reliable DNSBL servers

## 📋 Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## ToDo

📋 Alternative Blacklist Checking Techniques

Here are other methods beyond DNS-based blacklists for email deliverability checking:

1. API-Based Reputation Services

```php
// Example: Reputation services with REST APIs
$reputation_apis = [
    'Microsoft SNDS' => 'https://postmaster.live.com/snds/', // Requires signup
    'Google Postmaster Tools' => 'https://www.gmail.com/postmaster/',
    'Yahoo Sender Hub' => 'https://senders.yahooinc.com/',
    'Mailgun Reputation API' => 'https://api.mailgun.net/v4/ip/reputation',
    'SendGrid Reputation' => 'https://api.sendgrid.com/v3/ips/{ip}/reputation'
];
```

2. Multi-Service Aggregators

```php
// Services that check multiple blacklists via single API
$aggregator_services = [
    'MXToolbox API' => 'https://api.mxtoolbox.com/api/v1/monitor',
    'WhatIsMyIPAddress API' => 'https://api.whatismyipaddress.com/blacklist',
    'DNSlytics API' => 'https://api.dnslytics.com/v1/ip2blacklists',
    'HackerTarget API' => 'https://api.hackertarget.com/blacklistchecker/',
    'ipqualityscore.com' => 'https://ipqualityscore.com/api/json/ip/{api_key}/{ip}'
];
```

3. SURBL/URIBL Domain Checking

```php
// Check domains/URLs instead of IPs
$domain_blacklists = [
    'SURBL Multi' => 'multi.surbl.org',
    'URIBL Multi' => 'multi.uribl.com',
    'URIBL Black' => 'black.uribl.com',
    'URIBL Red' => 'red.uribl.com',
    'Spamhaus DBL' => 'dbl.spamhaus.org'
];

// Example usage: Check if domain is blacklisted
function checkDomainBlacklist($domain, $blacklist) {
    $query = $domain . '.' . $blacklist . '.';
    return checkdnsrr($query, 'A');
}
```

4. SMTP Test Connections

```php
// Test actual SMTP delivery capability
function testSmtpDelivery($ip, $targetMx) {
    $socket = fsockopen($targetMx, 25, $errno, $errstr, 10);
    if (!$socket) return false;

    $response = fgets($socket);
    if (strpos($response, '220') !== 0) {
        fclose($socket);
        return false;
    }

    fputs($socket, "HELO test.com\r\n");
    $response = fgets($socket);

    fclose($socket);
    return strpos($response, '250') === 0;
}
```

5. GeoIP & ASN-Based Checks

```php
// Check IP geography and network ownership
$geolocation_services = [
    'MaxMind GeoIP2' => 'https://dev.maxmind.com/geoip/geoip2/',
    'IPinfo.io' => 'https://ipinfo.io/{ip}/json',
    'ip-api.com' => 'http://ip-api.com/json/{ip}',
    'IPGeolocation.io' => 'https://api.ipgeolocation.io/ipgeo'
];

// Example: Block specific countries/ASNs known for spam
function checkGeoReputation($ip) {
    $high_risk_countries = ['CN', 'RU', 'KP']; // Example
    $high_risk_asns = ['AS12345', 'AS67890']; // Example spam networks

    // Implementation would check against these lists
    return ['risk_level' => 'low', 'country' => 'US', 'asn' => 'AS15169'];
}
```

6. Machine Learning Reputation Scoring

```php
// AI/ML-based reputation services
$ml_reputation_services = [
    'AWS GuardDuty' => 'https://aws.amazon.com/guardduty/',
    'Microsoft Defender' => 'https://docs.microsoft.com/en-us/microsoft-365/security/',
    'Cisco Talos' => 'https://talosintelligence.com/reputation_center',
    'VirusTotal' => 'https://www.virustotal.com/api/v3/ip_addresses/{ip}',
    'AbuseIPDB' => 'https://api.abuseipdb.com/api/v2/check'
];
```

7. Real-Time Threat Intelligence

```php
// Live threat feeds and intelligence services
$threat_intelligence = [
    'Emerging Threats' => 'https://rules.emergingthreats.net/',
    'AlienVault OTX' => 'https://otx.alienvault.com/api/v1/indicators/',
    'ThreatCrowd' => 'https://www.threatcrowd.org/searchApi/v2/ip/report/',
    'IBM X-Force' => 'https://api.xforce.ibmcloud.com/ipr/{ip}',
    'Shodan' => 'https://api.shodan.io/shodan/host/{ip}'
];
```

8. Email Authentication Checks

```php
// Verify SPF, DKIM, DMARC configuration
function checkEmailAuthentication($domain) {
    $checks = [];

    // SPF Record
    $spf = dns_get_record($domain, DNS_TXT);
    $checks['spf'] = array_filter($spf, fn($r) => str_contains($r['txt'], 'v=spf1'));

    // DMARC Record  
    $dmarc = dns_get_record('_dmarc.' . $domain, DNS_TXT);
    $checks['dmarc'] = array_filter($dmarc, fn($r) => str_contains($r['txt'], 'v=DMARC1'));

    // DKIM (requires knowing selector)
    // $dkim = dns_get_record('selector._domainkey.' . $domain, DNS_TXT);

    return $checks;
}
```

9. Historical Analysis

```php
// Track IP reputation over time
$historical_services = [
    'Passive DNS' => 'https://www.circl.lu/services/passive-dns/',
    'SecurityTrails' => 'https://api.securitytrails.com/v1/history/{ip}/a',
    'WhoisXML API' => 'https://reverse-ip-api.whoisxmlapi.com/api/v1',
    'DomainTools' => 'https://api.domaintools.com/v1/{ip}/host-domains/'
];
```

10. Integration Examples

```php
// Combined approach using multiple techniques
class ComprehensiveReputationChecker {
    public function checkReputation($ip) {
        return [
            'dnsbl' => $this->checkDnsbl($ip),           // Your current implementation
            'api_reputation' => $this->checkApiReputation($ip),
            'smtp_test' => $this->testSmtpConnectivity($ip),
            'geo_analysis' => $this->checkGeoReputation($ip),
            'threat_intel' => $this->checkThreatIntelligence($ip),
            'final_score' => $this->calculateFinalScore()
        ];
    }
}
```

## 🤝 Contributing

We welcome contributions! Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

### Development Setup

```bash
# Clone the repository
git clone https://github.com/palpalani/laravel-dns-deny-list-check.git
cd laravel-dns-deny-list-check

# Install dependencies
composer install

# Run tests
composer test

# Run static analysis
composer analyse

# Fix code style with Laravel Pint
composer format
```

### Contribution Guidelines

- ✅ Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards (enforced by Laravel Pint)
- ✅ Write tests for new features using Pest
- ✅ Ensure PHPStan analysis passes (`composer analyse`)
- ✅ Update documentation accordingly
- ✅ Ensure all tests pass before submitting PR
- ✅ Follow semantic versioning for releases

## 🔐 Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

For security-related issues, please email security@palpalani.com instead of using the issue tracker.

## 📦 Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/palpalani/laravel-dns-deny-list-check/tags).

## 👥 Credits

- [palPalani](https://github.com/palpalani) - Creator and maintainer
- [All Contributors](../../contributors) - Thank you for your contributions!

### Acknowledgments

- [Spatie](https://spatie.be) - For excellent Laravel package tools and inspiration
- DNSBL Service Providers - For maintaining public blacklist services
- Laravel Community - For feedback and testing

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---

<div align="center">
  <strong>🚀 Ready to ensure email deliverability?</strong><br>
  Install Laravel DNS Deny List Check and start protecting your email reputation today!
</div>
