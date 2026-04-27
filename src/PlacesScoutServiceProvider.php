<?php

declare(strict_types=1);

namespace IllumaLaw\PlacesScout;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PlacesScoutServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('places-scout')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton('places-scout', function (Application $app): PlacesScoutService {
            /** @var Repository $config */
            $config = $app->make('config');

            /** @var string|null $apiKey */
            $apiKey = $config->get('places-scout.api_key');

            return new PlacesScoutService($apiKey);
        });
    }
}
