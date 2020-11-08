<?php

declare(strict_types=1);


namespace Ralbear\EventsToSns\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Ralbear\EventsToSns\EventsToSnsProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            EventsToSnsProvider::class,
        ];
    }
}
