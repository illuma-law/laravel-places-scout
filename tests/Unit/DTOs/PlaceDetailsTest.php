<?php

declare(strict_types=1);

use IllumaLaw\PlacesScout\DTOs\PlaceDetails;
use IllumaLaw\PlacesScout\DTOs\PlaceSearchResult;

it('creates place details from array with all fields', function (): void {
    $data = [
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
    ];

    $details = PlaceDetails::fromArray($data);

    expect($details)
        ->name->toBe('Google Australia')
        ->formattedAddress->toBe('48 Pirrama Rd, Pyrmont NSW 2009, Australia')
        ->phoneNumber->toBe('(02) 9374 4000')
        ->website->toBe('https://www.google.com.au/')
        ->rating->toBe(4.4)
        ->userRatingsTotal->toBe(123)
        ->latitude->toBe(-33.866651)
        ->longitude->toBe(151.195827);
});

it('creates place details from array with minimal fields', function (): void {
    $data = [
        'name' => 'Test Place',
    ];

    $details = PlaceDetails::fromArray($data);

    expect($details)
        ->name->toBe('Test Place')
        ->formattedAddress->toBeNull()
        ->phoneNumber->toBeNull()
        ->website->toBeNull()
        ->rating->toBeNull()
        ->userRatingsTotal->toBeNull()
        ->latitude->toBeNull()
        ->longitude->toBeNull();
});

it('creates place details with partial geometry', function (): void {
    $data = [
        'name' => 'Test Place',
        'geometry' => [
            'location' => [],
        ],
    ];

    $details = PlaceDetails::fromArray($data);

    expect($details)
        ->latitude->toBeNull()
        ->longitude->toBeNull();
});

it('handles zero rating correctly', function (): void {
    $data = [
        'name' => 'Test Place',
        'rating' => 0,
    ];

    $details = PlaceDetails::fromArray($data);

    expect($details->rating)->toBe(0.0);
});

it('handles zero user ratings total correctly', function (): void {
    $data = [
        'name' => 'Test Place',
        'user_ratings_total' => 0,
    ];

    $details = PlaceDetails::fromArray($data);

    expect($details->userRatingsTotal)->toBe(0);
});

it('handles zero latitude and longitude correctly', function (): void {
    $data = [
        'name' => 'Test Place',
        'geometry' => [
            'location' => [
                'lat' => 0.0,
                'lng' => 0.0,
            ],
        ],
    ];

    $details = PlaceDetails::fromArray($data);

    expect($details)
        ->latitude->toBe(0.0)
        ->longitude->toBe(0.0);
});

it('converts rating to float', function (): void {
    $data = [
        'name' => 'Test Place',
        'rating' => '4.5',
    ];

    $details = PlaceDetails::fromArray($data);

    expect($details->rating)->toBe(4.5);
});

it('converts latitude and longitude to float', function (): void {
    $data = [
        'name' => 'Test Place',
        'geometry' => [
            'location' => [
                'lat' => '40.7128',
                'lng' => '-74.0060',
            ],
        ],
    ];

    $details = PlaceDetails::fromArray($data);

    expect($details)
        ->latitude->toBe(40.7128)
        ->longitude->toBe(-74.006);
});

it('is readonly and immutable', function (): void {
    $data = [
        'name' => 'Test Place',
    ];

    $details = PlaceDetails::fromArray($data);

    expect($details->name)->toBe('Test Place');
});

it('handles empty geometry gracefully', function (): void {
    $data = [
        'name' => 'Test Place',
        'geometry' => [],
    ];

    $details = PlaceDetails::fromArray($data);

    expect($details)
        ->latitude->toBeNull()
        ->longitude->toBeNull();
});

it('handles missing geometry key gracefully', function (): void {
    $data = [
        'name' => 'Test Place',
    ];

    $details = PlaceDetails::fromArray($data);

    expect($details)
        ->latitude->toBeNull()
        ->longitude->toBeNull();
});

it('has correct class structure', function (): void {
    expect(PlaceDetails::class)
        ->toBeReadonly()
        ->toHaveConstructor()
        ->toHaveMethod('fromArray')
        ->toHaveMethod('fromSearchResult')
        ->toHaveMethod('mergeWith')
        ->toHaveMethod('toArray');
});

it('creates place details from search result', function (): void {
    $result = new PlaceSearchResult(
        placeId: 'place-abc',
        name: 'Test Firm',
        formattedAddress: 'Rua Teste 1, Lisbon',
        rating: 4.2,
        userRatingsTotal: 55,
        latitude: 38.7,
        longitude: -9.1,
    );

    $details = PlaceDetails::fromSearchResult($result);

    expect($details)
        ->name->toBe('Test Firm')
        ->formattedAddress->toBe('Rua Teste 1, Lisbon')
        ->phoneNumber->toBeNull()
        ->website->toBeNull()
        ->rating->toBe(4.2)
        ->userRatingsTotal->toBe(55)
        ->latitude->toBe(38.7)
        ->longitude->toBe(-9.1);
});

it('mergeWith fills null fields from search result fallback', function (): void {
    $details = PlaceDetails::fromArray([
        'name' => 'Full Details',
        'formatted_phone_number' => '+351 910 000 000',
        'website' => 'https://example.pt',
    ]);

    $result = new PlaceSearchResult(
        placeId: 'place-abc',
        name: 'Search Name',
        formattedAddress: 'Rua Fallback 2',
        rating: 3.8,
        userRatingsTotal: 20,
        latitude: 40.0,
        longitude: -8.0,
    );

    $merged = $details->mergeWith($result);

    expect($merged)
        ->name->toBe('Full Details')
        ->formattedAddress->toBe('Rua Fallback 2')
        ->phoneNumber->toBe('+351 910 000 000')
        ->website->toBe('https://example.pt')
        ->rating->toBe(3.8)
        ->userRatingsTotal->toBe(20)
        ->latitude->toBe(40.0)
        ->longitude->toBe(-8.0);
});

it('mergeWith uses details fields when they are set', function (): void {
    $details = PlaceDetails::fromArray([
        'name' => 'Detailed Name',
        'formatted_address' => 'Rua Detalhada 5',
        'rating' => 4.9,
        'user_ratings_total' => 300,
        'geometry' => ['location' => ['lat' => 39.0, 'lng' => -7.5]],
    ]);

    $result = new PlaceSearchResult(
        placeId: 'place-xyz',
        name: 'Ignored Name',
        formattedAddress: 'Ignored Address',
        rating: 1.0,
        userRatingsTotal: 1,
        latitude: 0.0,
        longitude: 0.0,
    );

    $merged = $details->mergeWith($result);

    expect($merged)
        ->name->toBe('Detailed Name')
        ->formattedAddress->toBe('Rua Detalhada 5')
        ->rating->toBe(4.9)
        ->userRatingsTotal->toBe(300)
        ->latitude->toBe(39.0)
        ->longitude->toBe(-7.5);
});

it('mergeWith returns a new instance and does not mutate original', function (): void {
    $details = PlaceDetails::fromArray(['name' => 'Original']);

    $result = new PlaceSearchResult(
        placeId: 'p',
        name: 'Result',
        rating: 5.0,
    );

    $merged = $details->mergeWith($result);

    expect($merged)->not->toBe($details)
        ->and($details->rating)->toBeNull()
        ->and($merged->rating)->toBe(5.0);
});

it('toArray returns snake_case array', function (): void {
    $details = PlaceDetails::fromArray([
        'name' => 'Test Firm',
        'formatted_address' => 'Rua 1',
        'formatted_phone_number' => '+351 900 000 000',
        'website' => 'https://test.pt',
        'rating' => 4.1,
        'user_ratings_total' => 12,
        'geometry' => ['location' => ['lat' => 38.5, 'lng' => -9.0]],
    ]);

    expect($details->toArray())->toBe([
        'name' => 'Test Firm',
        'formatted_address' => 'Rua 1',
        'phone_number' => '+351 900 000 000',
        'website' => 'https://test.pt',
        'rating' => 4.1,
        'user_ratings_total' => 12,
        'latitude' => 38.5,
        'longitude' => -9.0,
    ]);
});

it('toArray returns nulls for missing optional fields', function (): void {
    $details = PlaceDetails::fromArray(['name' => 'Minimal']);

    expect($details->toArray())->toBe([
        'name' => 'Minimal',
        'formatted_address' => null,
        'phone_number' => null,
        'website' => null,
        'rating' => null,
        'user_ratings_total' => null,
        'latitude' => null,
        'longitude' => null,
    ]);
});
