<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AppTestCommand extends Command
{
    protected $signature = 'app:test-command';

    protected $description = 'A test application command';

    public function handle(): int
    {
        return self::SUCCESS;
    }
}
