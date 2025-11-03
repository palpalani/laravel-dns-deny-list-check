# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

### Testing
- `composer test` - Run PHPUnit tests
- `composer test-coverage` - Run tests with HTML coverage report (outputs to coverage/)

### Code Quality  
- `composer analyse` - Run PHPStan static analysis
- `composer format` - Run Laravel Pint to fix code style issues

## Architecture Overview

This is a Laravel package that provides DNS deny list (blacklist) checking functionality for IP addresses. The package checks IP addresses against multiple DNS-based blacklists (DNSBLs) to detect if they are listed on spam or abuse databases.

### Core Components

**Main Service Class (`src/DnsDenyListCheck.php`)**
- Contains the core `check(string $ip): array` method
- Validates IP addresses using `filter_var()`
- Performs reverse IP lookups against 80+ DNSBL servers
- Returns structured array with success status, message, and detailed results per DNSBL

**Service Provider (`src/DnsDenyListCheckServiceProvider.php`)**
- Standard Laravel package service provider using Spatie's Laravel Package Tools
- Publishes config file `dns-deny-list-check.php`
- No additional bindings or services registered

**Facade (`src/DnsDenyListCheckFacade.php`)**
- Laravel facade providing static access to the service
- Facade accessor: `dns-deny-list-check`

**Configuration (`config/dns-deny-list-check.php`)**
- Contains extensive list of DNSBL servers (380+ entries)
- Each server has `name` and `host` properties
- Currently not actively used by main service class (hardcoded list used instead)

### Package Structure
- Uses Orchestra Testbench for testing Laravel packages
- SQLite in-memory database for test environment
- No migrations or database tables required
- Pure DNS lookup functionality

### Key Technical Details
- Requires PHP 8.3+
- Compatible with Laravel 11.x/12.x
- Uses `checkdnsrr()` PHP function for DNS queries
- Implements reverse IP notation for DNSBL queries
- Handles exceptions gracefully with "Unknown" status
- No external HTTP dependencies - uses native DNS resolution

### Usage Pattern
```php
$checker = new DnsDenyListCheck();
$result = $checker->check('127.0.0.1');
// Returns: ['success' => bool, 'message' => string, 'data' => array]
```

### Development Notes
- Main service class has hardcoded DNSBL list that differs from config file
- Some DNSBL entries are commented out in the code
- Test suite uses standard Laravel package testing patterns
- No database interactions required for core functionality