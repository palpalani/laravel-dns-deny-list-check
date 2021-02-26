<?php

namespace PalPalani\LaravelDnsDenyListCheck;

use PalPalani\LaravelDnsDenyListCheck\Commands\LaravelDnsDenyListCheckCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelDnsDenyListCheckServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-dns-deny-list-check')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_dns_deny_list_check_table')
            ->hasCommand(LaravelDnsDenyListCheckCommand::class);
    }
}
