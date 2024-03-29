<?php

declare(strict_types=1);

namespace palPalani\DnsDenyListCheck;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DnsDenyListCheckServiceProvider extends PackageServiceProvider
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
            ->hasConfigFile('dns-deny-list-check');
    }
}
