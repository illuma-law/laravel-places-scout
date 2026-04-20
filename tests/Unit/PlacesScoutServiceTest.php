<?php

declare(strict_types=1);

use IllumaLaw\PlacesScout\DTOs\PlaceDetails;
use IllumaLaw\PlacesScout\DTOs\PlaceSearchResponse;
use IllumaLaw\PlacesScout\PlacesScoutService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

beforeEach(function (): void {
    config()->set('places-scout.api_key', 'test_api_key');
});

it('can be instantiated with api key', function (): void {
    $service = new PlacesScoutService('my_api_key');

    expect($service)->toBeInstanceOf(PlacesScoutService::class);
});

it('can be instantiated without api key', function (): void {
    $service = new PlacesScoutService();

    expect($service)->toBeInstanceOf(PlacesScoutService::class);
});

it('can set api key fluently with withApiKey', function (): void {
    $service = new PlacesScoutService();

    $result = $service->withApiKey('new_api_key');

    expect($result)->toBeInstanceOf(PlacesScoutService::class);
});

it('returns same instance when calling withApiKey', function (): void {
    $service = new PlacesScoutService();

    $result = $service->withApiKey('new_api_key');

    expect($result)->toBe($service);
});

it('performs text search successfully', function (): void {
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
            'next_page_token' => 'next_token',
            'status' => 'OK',
        ]),
    ]);

    $service = new PlacesScoutService('test_key');
    $response = $service->textSearch('Google Australia');

    expect($response)
        ->toBeInstanceOf(PlaceSearchResponse::class)
        ->and($response->results)->toHaveCount(1)
        ->and($response->results[0]->name)->toBe('Google Australia');
});

it('performs text search with page token', function (): void {
    Http::fake([
        'maps.googleapis.com/*' => Http::response([
            'results' => [],
            'status' => 'OK',
        ]),
    ]);

    $service = new PlacesScoutService('test_key');
    $service->textSearch('query', 'page_token_123');

    Http::assertSent(function ($request): bool {
        return str_contains($request->url(), 'maps.googleapis.com/maps/api/place/textsearch/json')
            && $request->data()['pagetoken'] === 'page_token_123'
            && $request->data()['query'] === 'query'
            && $request->data()['key'] === 'test_key';
    });
});

it('performs text search without page token', function (): void {
    Http::fake([
        'maps.googleapis.com/*' => Http::response([
            'results' => [],
            'status' => 'OK',
        ]),
    ]);

    $service = new PlacesScoutService('test_key');
    $service->textSearch('query');

    Http::assertSent(function ($request): bool {
        $data = $request->data();

        return str_contains($request->url(), 'maps.googleapis.com/maps/api/place/textsearch/json')
            && ! isset($data['pagetoken'])
            && $data['query'] === 'query'
            && $data['key'] === 'test_key';
    });
});

it('returns null when text search fails with http error', function (): void {
    Log::spy();

    Http::fake([
        'maps.googleapis.com/*' => Http::response(['error' => 'Some error'], 500),
    ]);

    $service = new PlacesScoutService('test_key');
    $response = $service->textSearch('fail query');

    expect($response)->toBeNull();
});

it('logs error when text search fails', function (): void {
    Log::shouldReceive('error')
        ->once()
        ->with('Google Places API textSearch failed', \Mockery::on(function ($context): bool {
            return $context['status'] === 500
                && $context['body'] === '{"error":"Some error"}'
                && $context['query'] === 'fail query';
        }));

    Http::fake([
        'maps.googleapis.com/*' => Http::response(['error' => 'Some error'], 500),
    ]);

    $service = new PlacesScoutService('test_key');
    $service->textSearch('fail query');
});

it('returns null when text search returns null data', function (): void {
    Http::fake([
        'maps.googleapis.com/*' => Http::response(null, 200),
    ]);

    $service = new PlacesScoutService('test_key');
    $response = $service->textSearch('query');

    expect($response)->toBeNull();
});

it('gets place details successfully', function (): void {
    Http::fake([
        'maps.googleapis.com/*' => Http::response([
            'result' => [
                'name' => 'Google Australia',
                'formatted_address' => '48 Pirrama Rd, Pyrmont NSW 2009, Australia',
                'formatted_phone_number' => '(02) 9374 4000',
                'website' => 'https://www.google.com.au/',
                'rating' => 4.4,
                'user_ratings_total' => 123,
                'geometry' => [
                    'location' => [
                        'lat' => -33.866651,
                        'lng' => 151.195827,
                    ],
                ],
            ],
            'status' => 'OK',
        ]),
    ]);

    $service = new PlacesScoutService('test_key');
    $details = $service->getPlaceDetails('ChIJN1t_tDeuEmsRUsoyG83frY4');

    expect($details)
        ->toBeInstanceOf(PlaceDetails::class)
        ->and($details->name)->toBe('Google Australia')
        ->and($details->phoneNumber)->toBe('(02) 9374 4000');
});

it('sends correct fields parameter for place details', function (): void {
    Http::fake([
        'maps.googleapis.com/*' => Http::response([
            'result' => ['name' => 'Test'],
            'status' => 'OK',
        ]),
    ]);

    $service = new PlacesScoutService('test_key');
    $service->getPlaceDetails('place_123');

    Http::assertSent(function ($request): bool {
        $data = $request->data();

        return str_contains($request->url(), 'maps.googleapis.com/maps/api/place/details/json')
            && $data['place_id'] === 'place_123'
            && $data['fields'] === 'name,formatted_address,formatted_phone_number,website,rating,user_ratings_total,geometry'
            && $data['key'] === 'test_key';
    });
});

it('returns null when place details fails with http error', function (): void {
    Log::spy();

    Http::fake([
        'maps.googleapis.com/*' => Http::response(['error' => 'Not found'], 404),
    ]);

    $service = new PlacesScoutService('test_key');
    $details = $service->getPlaceDetails('invalid_place_id');

    expect($details)->toBeNull();
});

it('returns null when place details result is missing', function (): void {
    Http::fake([
        'maps.googleapis.com/*' => Http::response([
            'status' => 'NOT_FOUND',
        ]),
    ]);

    $service = new PlacesScoutService('test_key');
    $details = $service->getPlaceDetails('invalid_place_id');

    expect($details)->toBeNull();
});

it('logs error when place details fails', function (): void {
    Log::shouldReceive('error')
        ->once()
        ->with('Google Places API getPlaceDetails failed', \Mockery::on(function ($context): bool {
            return $context['status'] === 500
                && $context['place_id'] === 'place_123';
        }));

    Http::fake([
        'maps.googleapis.com/*' => Http::response(['error' => 'Server error'], 500),
    ]);

    $service = new PlacesScoutService('test_key');
    $service->getPlaceDetails('place_123');
});

it('uses injected api key from constructor', function (): void {
    Http::fake([
        'maps.googleapis.com/*' => Http::response([
            'results' => [],
            'status' => 'OK',
        ]),
    ]);

    $service = new PlacesScoutService('constructor_key');
    $service->textSearch('query');

    Http::assertSent(function ($request): bool {
        return $request->data()['key'] === 'constructor_key';
    });
});

it('uses api key set via withApiKey over constructor key', function (): void {
    Http::fake([
        'maps.googleapis.com/*' => Http::response([
            'results' => [],
            'status' => 'OK',
        ]),
    ]);

    $service = new PlacesScoutService('constructor_key');
    $service->withApiKey('fluent_key');
    $service->textSearch('query');

    Http::assertSent(function ($request): bool {
        return $request->data()['key'] === 'fluent_key';
    });
});

it('handles empty response body in error logging for text search', function (): void {
    Log::shouldReceive('error')->once();

    Http::fake([
        'maps.googleapis.com/*' => Http::response('', 500),
    ]);

    $service = new PlacesScoutService('test_key');
    $service->textSearch('query');
});

it('handles empty response body in error logging for place details', function (): void {
    Log::shouldReceive('error')->once();

    Http::fake([
        'maps.googleapis.com/*' => Http::response('', 500),
    ]);

    $service = new PlacesScoutService('test_key');
    $service->getPlaceDetails('place_123');
});

it('handles empty result key in place details response', function (): void {
    Http::fake([
        'maps.googleapis.com/*' => Http::response([
            'status' => 'INVALID_REQUEST',
        ]),
    ]);

    $service = new PlacesScoutService('test_key');
    $result = $service->getPlaceDetails('place_123');

    expect($result)->toBeNull();
});
