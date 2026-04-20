<?php

declare(strict_types=1);

use IllumaLaw\PlacesScout\DTOs\PlaceSearchResponse;
use IllumaLaw\PlacesScout\DTOs\PlaceSearchResult;

it('creates place search response from array with results', function (): void {
    $data = [
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
        'next_page_token' => 'next_token_123',
        'status' => 'OK',
    ];

    $response = PlaceSearchResponse::fromArray($data);

    expect($response->results)->toHaveCount(1);
    expect($response->results[0])->not->toBeNull();
    expect($response->nextPageToken)->toBe('next_token_123');
    expect($response->status)->toBe('OK');
});

it('creates place search response from array with multiple results', function (): void {
    $data = [
        'results' => [
            [
                'place_id' => 'ChIJN1t_tDeuEmsRUsoyG83frY4',
                'name' => 'Google Australia',
            ],
            [
                'place_id' => 'ChIJN1t_tDeuEmsRUsoyG83frY5',
                'name' => 'Another Place',
            ],
        ],
        'status' => 'OK',
    ];

    $response = PlaceSearchResponse::fromArray($data);

    expect($response)
        ->results->toHaveCount(2);

    expect($response->results[0]->placeId)->toBe('ChIJN1t_tDeuEmsRUsoyG83frY4');
    expect($response->results[1]->name)->toBe('Another Place');
    expect($response->nextPageToken)->toBeNull();
});

it('creates place search response with empty results', function (): void {
    $data = [
        'results' => [],
        'status' => 'ZERO_RESULTS',
    ];

    $response = PlaceSearchResponse::fromArray($data);

    expect($response)
        ->results->toBe([])
        ->toHaveCount(0)
        ->nextPageToken->toBeNull()
        ->status->toBe('ZERO_RESULTS');
});

it('handles missing results key gracefully', function (): void {
    $data = [
        'status' => 'OK',
    ];

    $response = PlaceSearchResponse::fromArray($data);

    expect($response)
        ->results->toBe([])
        ->status->toBe('OK');
});

it('handles missing optional fields gracefully', function (): void {
    $data = [
        'results' => [],
    ];

    $response = PlaceSearchResponse::fromArray($data);

    expect($response)
        ->results->toBe([])
        ->nextPageToken->toBeNull()
        ->status->toBeNull();
});

it('has correct class structure', function (): void {
    expect(PlaceSearchResponse::class)
        ->toBeReadonly()
        ->toHaveConstructor()
        ->toHaveMethod('fromArray');
});
