<?php

declare(strict_types=1);

namespace IllumaLaw\PlacesScout\DTOs;

/**
 * Data Transfer Object for Google Place Details.
 */
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

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            formattedAddress: $data['formatted_address'] ?? null,
            phoneNumber: $data['formatted_phone_number'] ?? null,
            website: $data['website'] ?? null,
            rating: isset($data['rating']) ? (float) $data['rating'] : null,
            userRatingsTotal: isset($data['user_ratings_total']) ? (int) $data['user_ratings_total'] : null,
            latitude: isset($data['geometry']['location']['lat']) ? (float) $data['geometry']['location']['lat'] : null,
            longitude: isset($data['geometry']['location']['lng']) ? (float) $data['geometry']['location']['lng'] : null,
        );
    }
}
