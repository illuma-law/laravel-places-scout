<?php

declare(strict_types=1);

use IllumaLaw\PlacesScout\DTOs\PlaceSearchResult;

it('creates place search result from array with all fields', function (): void {
    $data = [
        'place_id' => 'ChIJN1t_tDeuEmsRUsoyG83frY4',
        'name' => 'Google Australia',
        'formatted_address' => '48 Pirrama Rd, Pyrmont NSW 2009, Australia',
        'rating' => 4.4,
        'user_ratings_total' => 123,
        'geometry' => [
            'location' => [
                'lat' => -33.866651,
                'lng' => 151.195827,
            ],
        ],
    ];

    $result = PlaceSearchResult::fromArray($data);

    expect($result)
        ->placeId->toBe('ChIJN1t_tDeuEmsRUsoyG83frY4')
        ->name->toBe('Google Australia')
        ->formattedAddress->toBe('48 Pirrama Rd, Pyrmont NSW 2009, Australia')
        ->rating->toBe(4.4)
        ->userRatingsTotal->toBe(123)
        ->latitude->toBe(-33.866651)
        ->longitude->toBe(151.195827);
});

it('creates place search result from array with minimal fields', function (): void {
    $data = [
        'place_id' => 'ChIJN1t_tDeuEmsRUsoyG83frY4',
        'name' => 'Test Place',
    ];

    $result = PlaceSearchResult::fromArray($data);

    expect($result)
        ->placeId->toBe('ChIJN1t_tDeuEmsRUsoyG83frY4')
        ->name->toBe('Test Place')
        ->formattedAddress->toBeNull()
        ->rating->toBeNull()
        ->userRatingsTotal->toBeNull()
        ->latitude->toBeNull()
        ->longitude->toBeNull();
});

it('handles zero values correctly', function (): void {
    $data = [
        'place_id' => 'ChIJN1t_tDeuEmsRUsoyG83frY4',
        'name' => 'Test Place',
        'rating' => 0,
        'user_ratings_total' => 0,
        'geometry' => [
            'location' => [
                'lat' => 0.0,
                'lng' => 0.0,
            ],
        ],
    ];

    $result = PlaceSearchResult::fromArray($data);

    expect($result)
        ->rating->toBe(0.0)
        ->userRatingsTotal->toBe(0)
        ->latitude->toBe(0.0)
        ->longitude->toBe(0.0);
});

it('converts numeric strings to correct types', function (): void {
    $data = [
        'place_id' => 'ChIJN1t_tDeuEmsRUsoyG83frY4',
        'name' => 'Test Place',
        'rating' => '4.5',
        'user_ratings_total' => '100',
        'geometry' => [
            'location' => [
                'lat' => '40.7128',
                'lng' => '-74.0060',
            ],
        ],
    ];

    $result = PlaceSearchResult::fromArray($data);

    expect($result)
        ->rating->toBe(4.5)
        ->userRatingsTotal->toBe(100)
        ->latitude->toBe(40.7128)
        ->longitude->toBe(-74.006);
});

it('handles missing optional geometry gracefully', function (): void {
    $data = [
        'place_id' => 'ChIJN1t_tDeuEmsRUsoyG83frY4',
        'name' => 'Test Place',
    ];

    $result = PlaceSearchResult::fromArray($data);

    expect($result)
        ->latitude->toBeNull()
        ->longitude->toBeNull();
});

it('has correct class structure', function (): void {
    expect(PlaceSearchResult::class)
        ->toBeReadonly()
        ->toHaveConstructor()
        ->toHaveMethod('fromArray')
        ->toHaveMethod('toArray');
});

it('toArray returns snake_case array', function (): void {
    $data = [
        'place_id' => 'ChIJN1t_tDeuEmsRUsoyG83frY4',
        'name' => 'Google Australia',
        'formatted_address' => '48 Pirrama Rd, Pyrmont NSW 2009, Australia',
        'rating' => 4.4,
        'user_ratings_total' => 123,
        'geometry' => ['location' => ['lat' => -33.866651, 'lng' => 151.195827]],
    ];

    $result = PlaceSearchResult::fromArray($data);

    expect($result->toArray())->toBe([
        'place_id' => 'ChIJN1t_tDeuEmsRUsoyG83frY4',
        'name' => 'Google Australia',
        'formatted_address' => '48 Pirrama Rd, Pyrmont NSW 2009, Australia',
        'rating' => 4.4,
        'user_ratings_total' => 123,
        'latitude' => -33.866651,
        'longitude' => 151.195827,
    ]);
});

it('toArray returns nulls for missing optional fields', function (): void {
    $result = PlaceSearchResult::fromArray([
        'place_id' => 'abc',
        'name' => 'Minimal',
    ]);

    expect($result->toArray())->toBe([
        'place_id' => 'abc',
        'name' => 'Minimal',
        'formatted_address' => null,
        'rating' => null,
        'user_ratings_total' => null,
        'latitude' => null,
        'longitude' => null,
    ]);
});
