# Email Deny List (blacklist) Check - IP Deny List (blacklist) Check

[![Latest Version on Packagist](https://img.shields.io/packagist/v/palpalani/laravel-dns-deny-list-check.svg?style=flat-square)](https://packagist.org/packages/palpalani/laravel-dns-deny-list-check)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/palpalani/laravel-dns-deny-list-check/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/palpalani/laravel-dns-deny-list-check/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/palpalani/laravel-dns-deny-list-check/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/palpalani/laravel-dns-deny-list-check/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/palpalani/laravel-dns-deny-list-check.svg?style=flat-square)](https://packagist.org/packages/palpalani/laravel-dns-deny-list-check)

Deny list (blacklist) checker will test a mail server IP address against over 50 DNS 
based email blacklists. (Commonly called Realtime blacklist, DNSBL or RBL).  
If your mail server has been blacklisted, some email you send may not be delivered.  
Email blacklists are a common way of reducing spam.

## Installation

You can install the package via composer:

```bash
composer require palpalani/laravel-dns-deny-list-check
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="palPalani\DnsDenyListCheck\DnsDenyListCheckServiceProvider" --tag="laravel-dns-deny-list-check-config"
```

This is the contents of the published config file:

```php
return [

];
```

## Usage

```php
$check = new palPalani\DnsDenyListCheck\DnsDenyListCheck();
echo $check->check('127.0.0.1');
```

## Testing

```bash
composer test
```

## Changelog

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

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/palpalani/laravel-dns-deny-list-check/tags).

## Credits

- [palPalani](https://github.com/palpalani)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
