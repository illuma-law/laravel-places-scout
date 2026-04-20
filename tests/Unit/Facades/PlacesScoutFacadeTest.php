<?php

declare(strict_types=1);

use IllumaLaw\PlacesScout\DTOs\PlaceDetails;
use IllumaLaw\PlacesScout\DTOs\PlaceSearchResponse;
use IllumaLaw\PlacesScout\Facades\PlacesScout;
use IllumaLaw\PlacesScout\PlacesScoutService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

it('resolves facade to service instance', function (): void {
    $instance = PlacesScout::getFacadeRoot();

    expect($instance)->toBeInstanceOf(PlacesScoutService::class);
});

it('can perform text search via facade', function (): void {
    Http::fake([
        'maps.googleapis.com/*' => Http::response([
            'results' => [
                [
                    'place_id' => 'ChIJN1t_tDeuEmsRUsoyG83frY4',
                    'name' => 'Google Australia',
                    'formatted_address' => '48 Pirrama Rd, Pyrmont NSW 2009, Australia',
                    'geometry' => [
                        'location' => [
                            'lat' => -33.866651,
                            'lng' => 151.195827,
                        ],
                    ],
                ],
            ],
            'status' => 'OK',
        ]),
    ]);

    $response = PlacesScout::textSearch('Google Australia');

    expect($response)->not->toBeNull();
    /** @var PlaceSearchResponse $response */
    expect($response)
        ->and($response->results[0]->name)->toBe('Google Australia');
});

it('can get place details via facade', function (): void {
    Http::fake([
        'maps.googleapis.com/*' => Http::response([
            'result' => [
                'name' => 'Google Australia',
                'formatted_address' => '48 Pirrama Rd, Pyrmont NSW 2009, Australia',
            ],
            'status' => 'OK',
        ]),
    ]);

    $details = PlacesScout::getPlaceDetails('ChIJN1t_tDeuEmsRUsoyG83frY4');

    expect($details)->not->toBeNull();
    /** @var PlaceDetails $details */
    expect($details)
        ->and($details->name)->toBe('Google Australia');
});

it('can use fluent withApiKey via facade', function (): void {
    Http::fake([
        'maps.googleapis.com/*' => Http::response([
            'results' => [],
            'status' => 'OK',
        ]),
    ]);

    PlacesScout::withApiKey('custom_key')->textSearch('query');

    Http::assertSent(function (Request $request): bool {
        return $request->data()['key'] === 'custom_key';
    });
});

it('uses configured api key by default', function (): void {
    Http::fake([
        'maps.googleapis.com/*' => Http::response([
            'results' => [],
            'status' => 'OK',
        ]),
    ]);

    PlacesScout::textSearch('query');

    Http::assertSent(function (Request $request): bool {
        return $request->data()['key'] === 'test_api_key';
    });
});

it('returns correct facade accessor', function (): void {
    // Test the Facade class directly using reflection
    $reflection = new ReflectionClass(PlacesScout::class);
    $method = $reflection->getMethod('getFacadeAccessor');
    $accessor = $method->invoke(null);

    expect($accessor)->toBe('places-scout');
});

it('has correct facade docblock methods', function (): void {
    $reflection = new ReflectionClass(PlacesScout::class);
    $docComment = $reflection->getDocComment();

    expect((string) $docComment)
        ->toContain('textSearch')
        ->toContain('getPlaceDetails')
        ->toContain('withApiKey')
        ->toContain('PlaceSearchResponse')
        ->toContain('PlaceDetails')
        ->toContain('PlacesScoutService');
});

it('resolves same service instance on multiple calls', function (): void {
    $instance1 = PlacesScout::getFacadeRoot();
    $instance2 = PlacesScout::getFacadeRoot();

    expect($instance1)->toBe($instance2);
});

it('can chain methods after withApiKey', function (): void {
    Http::fake([
        'maps.googleapis.com/*' => Http::response([
            'result' => ['name' => 'Test Place'],
            'status' => 'OK',
        ]),
    ]);

    $result = PlacesScout::withApiKey('chained_key')->getPlaceDetails('place_123');

    expect($result)->toBeInstanceOf(PlaceDetails::class);
    Http::assertSent(fn (Request $request): bool => $request->data()['key'] === 'chained_key');
});
