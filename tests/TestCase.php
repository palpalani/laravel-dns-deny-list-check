<?php

declare(strict_types=1);

namespace palPalani\DnsDenyListCheck\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use palPalani\DnsDenyListCheck\DnsDenyListCheckServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            DnsDenyListCheckServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        /*
        include_once __DIR__.'/../database/migrations/create_laravel_dns_deny_list_check_table.php.stub';
        (new \CreatePackageTable())->up();
        */
    }
}
