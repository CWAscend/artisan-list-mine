<?php

namespace Ascend\ArtisanListMine;

use Illuminate\Console\Application as Artisan;
use Illuminate\Support\ServiceProvider;

class ArtisanListMineServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/artisan-list-mine.php',
            'artisan-list-mine'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/artisan-list-mine.php' => config_path('artisan-list-mine.php'),
            ], 'artisan-list-mine-config');
        }

        Artisan::starting(function ($artisan) {
            $artisan->add($this->app->make(ListCommand::class));
        });
    }
}
