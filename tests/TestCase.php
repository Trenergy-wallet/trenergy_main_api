<?php
declare(strict_types=1);

namespace Apd\Trenergy\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Apd\Trenergy\Providers\TrenergyServiceProvider;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [TrenergyServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('trenergy.base-url', 'https://api.test.trenergy.com');
        $app['config']->set('trenergy.api-key', 'test-api-key');
        $app['config']->set('trenergy.headers', [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ]);
    }
}
