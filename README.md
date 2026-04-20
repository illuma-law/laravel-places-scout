# Laravel Places Scout

[![Latest Version on Packagist](https://img.shields.io/packagist/v/illuma-law/laravel-places-scout.svg?style=flat-square)](https://packagist.org/packages/illuma-law/laravel-places-scout)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/illuma-law/laravel-places-scout/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/illuma-law/laravel-places-scout/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub PHPStan Action Status](https://img.shields.io/github/actions/workflow/status/illuma-law/laravel-places-scout/phpstan.yml?branch=main&label=phpstan&style=flat-square)](https://github.com/illuma-law/laravel-places-scout/actions?query=workflow%3Aphpstan+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/illuma-law/laravel-places-scout.svg?style=flat-square)](https://packagist.org/packages/illuma-law/laravel-places-scout)
[![License](https://img.shields.io/packagist/l/illuma-law/laravel-places-scout.svg?style=flat-square)](https://packagist.org/packages/illuma-law/laravel-places-scout)

A fluent Laravel package for interacting with the Google Places API with strongly typed DTOs.

## TL;DR

```php
use IllumaLaw\PlacesScout\Facades\PlacesScout;

// Search for places using a text query
$response = PlacesScout::textSearch('Law firms in New York');

foreach ($response->results as $result) {
    echo $result->name;
    echo $result->formattedAddress;
}
```

## Features

- 🔍 **Text Search**: Search for places using text queries with pagination support
- 📍 **Place Details**: Retrieve detailed information about specific places
- 🎯 **Strongly Typed DTOs**: Immutable data transfer objects with full IDE autocompletion
- 🔄 **Fluent Interface**: Chain methods for clean, readable code
- 🔐 **Configurable API Keys**: Use global config or override per-request
- 🧪 **100% Test Coverage**: Comprehensive test suite with Pest
- 📊 **PHPStan Level max**: Static analysis for maximum code quality
- ⚡ **PHP 8.3+**: Leverages modern PHP features like readonly classes and named arguments

## Requirements

- PHP 8.3 or higher
- Laravel 11.x, 12.x, or 13.x

## Installation

You can install the package via composer:

```bash
composer require illuma-law/laravel-places-scout
```

## Configuration

You can publish the config file with:

```bash
php artisan vendor:publish --tag="places-scout-config"
```

This is the contents of the published config file:

```php
<?php

return [
    /**
     * Your Google Places API Key.
     *
     * You can obtain an API key from the Google Cloud Console:
     * https://console.cloud.google.com/google/maps-apis/credentials
     */
    'api_key' => env('GOOGLE_PLACES_API_KEY'),
];
```

Add your Google Places API key to your `.env` file:

```env
GOOGLE_PLACES_API_KEY=your_api_key_here
```

## Usage

### Text Search

Search for places using a text query. Returns a `PlaceSearchResponse` DTO containing a collection of `PlaceSearchResult` DTOs.

```php
use IllumaLaw\PlacesScout\Facades\PlacesScout;

// Basic text search
$response = PlacesScout::textSearch('Law firms in New York');

// Access results
foreach ($response->results as $result) {
    echo $result->name;           // 'Smith & Associates'
    echo $result->placeId;        // 'ChIJ...'
    echo $result->latitude;       // 40.7128
    echo $result->longitude;      // -74.0060
    echo $result->formattedAddress; // '123 Main St, New York, NY'
}

// Handle pagination
if ($response->nextPageToken) {
    $nextPage = PlacesScout::textSearch('Law firms in New York', $response->nextPageToken);
}
```

### Place Details

Get detailed information about a specific place using its `place_id`. Returns a `PlaceDetails` DTO.

```php
use IllumaLaw\PlacesScout\Facades\PlacesScout;

$details = PlacesScout::getPlaceDetails('ChIJN1t_tDeuEmsRUsoyG83frY4');

echo $details->name;              // 'Google Australia'
echo $details->phoneNumber;       // '(02) 9374 4000'
echo $details->website;           // 'https://www.google.com.au/'
echo $details->formattedAddress;  // '48 Pirrama Rd, Pyrmont NSW 2009, Australia'
echo $details->rating;            // 4.4
echo $details->userRatingsTotal;  // 123
echo $details->latitude;          // -33.866651
echo $details->longitude;         // 151.195827
```

### Using Different API Keys

You can override the configured API key on the fly:

```php
use IllumaLaw\PlacesScout\Facades\PlacesScout;

// Use a different API key for a single request
$details = PlacesScout::withApiKey('temporary-key')->getPlaceDetails('ChIJ...');

// The fluent interface supports chaining
$response = PlacesScout::withApiKey('another-key')
    ->textSearch('Restaurants in Paris');
```

### Dependency Injection

You can also inject the service directly:

```php
use IllumaLaw\PlacesScout\PlacesScoutService;

class PlaceController
{
    public function __construct(
        private PlacesScoutService $placesScout
    ) {}

    public function search(string $query)
    {
        $results = $this->placesScout->textSearch($query);

        return response()->json($results);
    }
}
```

## Data Transfer Objects

### PlaceSearchResponse

```php
final readonly class PlaceSearchResponse
{
    /**
     * @param array<PlaceSearchResult> $results
     */
    public function __construct(
        public array $results,
        public ?string $nextPageToken = null,
        public ?string $status = null,
    ) {}
}
```

### PlaceSearchResult

```php
final readonly class PlaceSearchResult
{
    public function __construct(
        public string $placeId,
        public string $name,
        public ?string $formattedAddress = null,
        public ?float $rating = null,
        public ?int $userRatingsTotal = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
    ) {}
}
```

### PlaceDetails

```php
final readonly class PlaceDetails
{
    public function __construct(
        public string $name,
        public ?string $formattedAddress = null,
        public ?string $phoneNumber = null,
        public ?string $website = null,
        public ?float $rating = null,
        public ?int $userRatingsTotal = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
    ) {}
}
```

## Error Handling

The package handles API errors gracefully by returning `null` when requests fail:

```php
$response = PlacesScout::textSearch('Invalid query that fails');

if ($response === null) {
    // Handle error - API request failed
}

$details = PlacesScout::getPlaceDetails('invalid-place-id');

if ($details === null) {
    // Place not found or API error
}
```

Failed requests are automatically logged using Laravel's logging system with contextual information about the error.

## Testing

Run the test suite with:

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage

# Run static analysis
composer analyse

# Run code formatting
composer format
```

### Test Coverage

This package maintains 100% test coverage using [Pest](https://pestphp.com/). All DTOs, services, facades, and service providers are thoroughly tested.

## Architecture

The package follows strict architectural principles:

- **Readonly DTOs**: All data objects are immutable `final readonly` classes
- **Strict Types**: All files use `declare(strict_types=1)`
- **Static Analysis**: PHPStan Level max compliance with Larastan
- **Code Style**: Laravel Pint for consistent formatting
- **Architecture Tests**: Pest architecture tests enforce dependency rules

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributions are welcome! Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Development Setup

1. Fork the repository
2. Clone your fork: `git clone https://github.com/illuma-law/laravel-places-scout.git`
3. Install dependencies: `composer install`
4. Run tests: `composer test`
5. Run static analysis: `composer analyse`
6. Format code: `composer format`

### Reporting Issues

If you discover any security-related issues, please email support@illuma.law instead of using the issue tracker.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [illuma-law](https://github.com/illuma-law)
- [All Contributors](../../contributors)

This package is built on top of excellent open-source software:

- [Laravel](https://laravel.com)
- [Spatie Laravel Package Tools](https://github.com/spatie/laravel-package-tools)
- [Pest](https://pestphp.com/)
- [PHPStan](https://phpstan.org/)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

Copyright (c) 2026 illuma-law
