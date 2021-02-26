<?php

namespace PalPalani\LaravelDnsDenyListCheck;

use Illuminate\Support\Facades\Facade;

/**
 * @see \PalPalani\LaravelDnsDenyListCheck\LaravelDnsDenyListCheck
 */
class LaravelDnsDenyListCheckFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-dns-deny-list-check';
    }
}
