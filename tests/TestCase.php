<?php

namespace Ascend\ArtisanListMine\Tests;

use Ascend\ArtisanListMine\ArtisanListMineServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ArtisanListMineServiceProvider::class,
        ];
    }
}
