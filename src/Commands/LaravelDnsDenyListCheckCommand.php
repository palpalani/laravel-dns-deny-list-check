<?php

namespace PalPalani\LaravelDnsDenyListCheck\Commands;

use Illuminate\Console\Command;

class LaravelDnsDenyListCheckCommand extends Command
{
    public $signature = 'laravel-dns-deny-list-check';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
