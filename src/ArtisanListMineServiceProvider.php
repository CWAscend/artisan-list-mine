<?php

namespace Ascend\ArtisanListMine;

use Illuminate\Console\Application as Artisan;
use Illuminate\Support\ServiceProvider;

class ArtisanListMineServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Artisan::starting(function ($artisan) {
            $artisan->add($this->app->make(ListCommand::class));
        });
    }
}
