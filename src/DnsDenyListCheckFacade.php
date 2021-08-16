<?php

declare(strict_types=1);

namespace palPalani\DnsDenyListCheck;

use Illuminate\Support\Facades\Facade;

/**
 * @see \palPalani\DnsDenyListCheck\DnsDenyListCheck
 */
class DnsDenyListCheckFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'dns-deny-list-check';
    }
}
