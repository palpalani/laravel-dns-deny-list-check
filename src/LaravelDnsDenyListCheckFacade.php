<?php

namespace palPalani\LaravelDnsDenyListCheck;

use Illuminate\Support\Facades\Facade;

/**
 * @see \palPalani\LaravelDnsDenyListCheck\LaravelDnsDenyListCheck
 */
class LaravelDnsDenyListCheckFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-dns-deny-list-check';
    }
}
