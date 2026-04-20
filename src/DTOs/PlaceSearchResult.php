<?php

declare(strict_types=1);

namespace IllumaLaw\PlacesScout\DTOs;

/**
 * Data Transfer Object for Google Place Search Result.
 */
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

    public static function fromArray(array $data): self
    {
        return new self(
            placeId: $data['place_id'],
            name: $data['name'],
            formattedAddress: $data['formatted_address'] ?? null,
            rating: isset($data['rating']) ? (float) $data['rating'] : null,
            userRatingsTotal: isset($data['user_ratings_total']) ? (int) $data['user_ratings_total'] : null,
            latitude: isset($data['geometry']['location']['lat']) ? (float) $data['geometry']['location']['lat'] : null,
            longitude: isset($data['geometry']['location']['lng']) ? (float) $data['geometry']['location']['lng'] : null,
        );
    }
}
