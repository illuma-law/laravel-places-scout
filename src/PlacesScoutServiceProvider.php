<?php

declare(strict_types=1);

namespace IllumaLaw\PlacesScout;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

/**
 * Service provider for the Laravel Places Scout package.
 */
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
        $this->app->singleton('places-scout', function (\Illuminate\Foundation\Application $app): PlacesScoutService {
            /** @var \Illuminate\Contracts\Config\Repository $config */
            $config = $app->make('config');

            /** @var string|null $apiKey */
            $apiKey = $config->get('places-scout.api_key');

            return new PlacesScoutService($apiKey);
        });
    }
}
