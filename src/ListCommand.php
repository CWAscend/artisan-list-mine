<?php

namespace Ascend\ArtisanListMine;

use Illuminate\Contracts\Foundation\Application;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\ListCommand as SymfonyListCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends SymfonyListCommand
{
    private string $appNamespace;

    private Application $laravel;

    public function __construct(Application $laravel)
    {
        parent::__construct();

        $this->laravel = $laravel;
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addOption(
            'mine',
            null,
            InputOption::VALUE_NONE,
            'Only show application commands (excludes vendor)'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $application = $this->getApplication();
        $mineOption = $this->getDefinition()->getOption('mine');

        // Temporarily add --mine to Application definition so it shows in output
        $application->getDefinition()->addOption($mineOption);

        try {
            if (! $input->getOption('mine')) {
                return parent::execute($input, $output);
            }

            return $this->executeWithMineFilter($input, $output);
        } finally {
            // Remove from Application definition
            $this->removeOptionFromDefinition($application, 'mine');
        }
    }

    private function executeWithMineFilter(InputInterface $input, OutputInterface $output): int
    {
        $this->appNamespace = $this->laravel->getNamespace();
        $application = $this->getApplication();
        $allCommands = $application->all();
        $hiddenStates = [];

        // Temporarily hide non-application commands
        foreach ($allCommands as $name => $command) {
            if (! $this->isApplicationCommand($command)) {
                $hiddenStates[$name] = $command->isHidden();
                $command->setHidden(true);
            }
        }

        try {
            return parent::execute($input, $output);
        } finally {
            // Restore original hidden states
            foreach ($hiddenStates as $name => $wasHidden) {
                if (isset($allCommands[$name])) {
                    $allCommands[$name]->setHidden($wasHidden);
                }
            }
        }
    }

    private function isApplicationCommand(Command $command): bool
    {
        if (str_starts_with(get_class($command), $this->appNamespace)) {
            return true;
        }

        // Check for Laravel Actions or other decorator patterns where
        // the command wraps an action/handler in the App namespace
        return $this->hasApplicationHandler($command);
    }

    private function hasApplicationHandler(Command $command): bool
    {
        $reflection = new ReflectionClass($command);

        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);

            if (! $property->isInitialized($command)) {
                continue;
            }

            $value = $property->getValue($command);

            if (is_object($value) && str_starts_with(get_class($value), $this->appNamespace)) {
                return true;
            }
        }

        return false;
    }

    private function removeOptionFromDefinition($application, string $name): void
    {
        $definition = $application->getDefinition();
        $reflection = new ReflectionClass($definition);
        $property = $reflection->getProperty('options');
        $property->setAccessible(true);
        $options = $property->getValue($definition);
        unset($options[$name]);
        $property->setValue($definition, $options);
    }
}
