<?php

namespace Vendor\SomePackage\Commands;

use Illuminate\Console\Command;

class VendorTestCommand extends Command
{
    protected $signature = 'vendor:test-command';

    protected $description = 'A test vendor command';

    public function handle(): int
    {
        return self::SUCCESS;
    }
}
