<?php

namespace Vendor\SomePackage\Commands;

use App\Actions\AppActionHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Simulates Laravel Actions' command wrapper - a vendor class
 * that holds an action from the App namespace.
 */
class ActionWrappedCommand extends Command
{
    public object $action;

    public function __construct()
    {
        parent::__construct('app:action-wrapped');

        $this->setDescription('A vendor command wrapping an App action');
        $this->action = new AppActionHandler;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return self::SUCCESS;
    }
}
