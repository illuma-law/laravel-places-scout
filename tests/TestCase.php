<?php

declare(strict_types=1);

namespace IllumaLaw\PlacesScout\Tests;

use IllumaLaw\PlacesScout\PlacesScoutServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * Base test case for the Laravel Places Scout package.
 */
abstract class TestCase extends Orchestra
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            PlacesScoutServiceProvider::class,
        ];
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     */
    public function getEnvironmentSetUp($app): void
    {
        config()->set('places-scout.api_key', 'test_api_key');
    }
}
