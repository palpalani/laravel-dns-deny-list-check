# Email Deny List (blacklist) Check - IP Deny List (blacklist) Check

[![Latest Version on Packagist](https://img.shields.io/packagist/v/palpalani/laravel-dns-deny-list-check.svg?style=flat-square)](https://packagist.org/packages/palpalani/laravel-dns-deny-list-check)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/palpalani/laravel-dns-deny-list-check/run-tests?label=tests)](https://github.com/palpalani/laravel-dns-deny-list-check/actions?query=workflow%3ATests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/palpalani/laravel-dns-deny-list-check/Check%20&%20fix%20styling?label=code%20style)](https://github.com/palpalani/laravel-dns-deny-list-check/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
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
