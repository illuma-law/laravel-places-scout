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

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        /** @var mixed $geometry */
        $geometry = $data['geometry'] ?? null;

        /** @var mixed $location */
        $location = is_array($geometry) ? ($geometry['location'] ?? null) : null;

        return new self(
            placeId: is_string($data['place_id'] ?? null) ? (string) $data['place_id'] : '',
            name: is_string($data['name'] ?? null) ? (string) $data['name'] : '',
            formattedAddress: is_string($data['formatted_address'] ?? null) ? (string) $data['formatted_address'] : null,
            rating: isset($data['rating']) && is_numeric($data['rating']) ? (float) $data['rating'] : null,
            userRatingsTotal: isset($data['user_ratings_total']) && is_numeric($data['user_ratings_total']) ? (int) $data['user_ratings_total'] : null,
            latitude: is_array($location) && isset($location['lat']) && is_numeric($location['lat']) ? (float) $location['lat'] : null,
            longitude: is_array($location) && isset($location['lng']) && is_numeric($location['lng']) ? (float) $location['lng'] : null,
        );
    }
}
