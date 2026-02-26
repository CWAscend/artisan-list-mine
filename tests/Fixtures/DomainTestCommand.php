<?php

namespace Domain\Commands;

use Illuminate\Console\Command;

class DomainTestCommand extends Command
{
    protected $signature = 'domain:test-command';

    protected $description = 'A test domain command';

    public function handle(): int
    {
        return self::SUCCESS;
    }
}
