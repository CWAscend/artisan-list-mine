<?php

namespace Ascend\ArtisanListMine\Tests;

use App\Console\Commands\AppTestCommand;
use Domain\Commands\DomainTestCommand;
use Vendor\SomePackage\Commands\ActionWrappedCommand;
use Vendor\SomePackage\Commands\VendorTestCommand;

class ListCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Register test commands
        $this->app->make('Illuminate\Contracts\Console\Kernel')->registerCommand(new AppTestCommand);
        $this->app->make('Illuminate\Contracts\Console\Kernel')->registerCommand(new VendorTestCommand);
        $this->app->make('Illuminate\Contracts\Console\Kernel')->registerCommand(new ActionWrappedCommand);
        $this->app->make('Illuminate\Contracts\Console\Kernel')->registerCommand(new DomainTestCommand);
    }

    public function test_list_command_without_mine_shows_all_commands(): void
    {
        $this->artisan('list')
            ->assertSuccessful()
            ->expectsOutputToContain('app:test-command')
            ->expectsOutputToContain('vendor:test-command');
    }

    public function test_list_command_with_mine_shows_only_application_commands(): void
    {
        $this->artisan('list', ['--mine' => true])
            ->assertSuccessful()
            ->expectsOutputToContain('app:test-command')
            ->doesntExpectOutputToContain('vendor:test-command');
    }

    public function test_list_command_with_mine_hides_built_in_laravel_commands(): void
    {
        $this->artisan('list', ['--mine' => true])
            ->assertSuccessful()
            ->doesntExpectOutputToContain('make:model')
            ->doesntExpectOutputToContain('migrate');
    }

    public function test_mine_option_shows_in_list_output(): void
    {
        $this->artisan('list')
            ->assertSuccessful()
            ->expectsOutputToContain('--mine');
    }

    public function test_mine_option_shows_in_help_output(): void
    {
        $this->artisan('help', ['command_name' => 'list'])
            ->assertSuccessful()
            ->expectsOutputToContain('--mine');
    }

    public function test_mine_option_only_defined_on_list_command(): void
    {
        // Verify --mine is defined on the list command
        $listCommand = $this->app->make('Illuminate\Contracts\Console\Kernel')
            ->all()['list'];

        $this->assertTrue($listCommand->getDefinition()->hasOption('mine'));

        // Verify --mine is NOT defined on other commands
        $helpCommand = $this->app->make('Illuminate\Contracts\Console\Kernel')
            ->all()['help'];

        $this->assertFalse($helpCommand->getDefinition()->hasOption('mine'));
    }

    public function test_list_command_restores_hidden_state_after_execution(): void
    {
        // Get a reference to a command that should be hidden with --mine
        $vendorCommand = $this->app->make('Illuminate\Contracts\Console\Kernel')
            ->all()['vendor:test-command'] ?? null;

        if ($vendorCommand) {
            $originalHiddenState = $vendorCommand->isHidden();

            $this->artisan('list', ['--mine' => true])->assertSuccessful();

            $this->assertEquals($originalHiddenState, $vendorCommand->isHidden());
        } else {
            $this->markTestSkipped('Vendor command not registered');
        }
    }

    public function test_list_command_with_mine_returns_success(): void
    {
        $this->artisan('list', ['--mine' => true])
            ->assertExitCode(0);
    }

    public function test_list_command_without_options_returns_success(): void
    {
        $this->artisan('list')
            ->assertExitCode(0);
    }

    public function test_vendor_command_with_app_action_is_shown_with_mine(): void
    {
        // ActionWrappedCommand is in Vendor namespace but wraps an App\Actions handler
        // This simulates Laravel Actions pattern
        $this->artisan('list', ['--mine' => true])
            ->assertSuccessful()
            ->expectsOutputToContain('app:action-wrapped');
    }

    public function test_vendor_command_without_app_handler_is_hidden_with_mine(): void
    {
        // VendorTestCommand has no App namespace handler, should be hidden
        $this->artisan('list', ['--mine' => true])
            ->assertSuccessful()
            ->doesntExpectOutputToContain('vendor:test-command');
    }

    public function test_domain_command_is_hidden_by_default_with_mine(): void
    {
        // Domain commands are not in the default App\ namespace
        $this->artisan('list', ['--mine' => true])
            ->assertSuccessful()
            ->doesntExpectOutputToContain('domain:test-command');
    }

    public function test_domain_command_is_shown_when_namespace_configured(): void
    {
        // Add Domain\ to configured namespaces
        $this->app->make('config')->set('artisan-list-mine.namespaces', [
            'App\\',
            'Domain\\',
        ]);

        $this->artisan('list', ['--mine' => true])
            ->assertSuccessful()
            ->expectsOutputToContain('domain:test-command');
    }

    public function test_app_command_hidden_when_removed_from_config(): void
    {
        // Configure only Domain\ namespace, removing App\
        $this->app->make('config')->set('artisan-list-mine.namespaces', [
            'Domain\\',
        ]);

        $this->artisan('list', ['--mine' => true])
            ->assertSuccessful()
            ->expectsOutputToContain('domain:test-command')
            ->doesntExpectOutputToContain('app:test-command');
    }

    public function test_config_file_can_be_published(): void
    {
        $this->artisan('vendor:publish', ['--tag' => 'artisan-list-mine-config'])
            ->assertSuccessful();
    }
}
