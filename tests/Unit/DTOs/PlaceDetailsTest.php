<?php

declare(strict_types=1);

use IllumaLaw\PlacesScout\DTOs\PlaceDetails;

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

    // Verify readonly by checking we can read but class is readonly
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
        ->toHaveMethod('fromArray');
});
