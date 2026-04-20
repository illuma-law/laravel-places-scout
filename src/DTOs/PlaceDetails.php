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
            name: is_string($data['name'] ?? null) ? (string) $data['name'] : '',
            formattedAddress: is_string($data['formatted_address'] ?? null) ? (string) $data['formatted_address'] : null,
            phoneNumber: is_string($data['formatted_phone_number'] ?? null) ? (string) $data['formatted_phone_number'] : null,
            website: is_string($data['website'] ?? null) ? (string) $data['website'] : null,
            rating: isset($data['rating']) && is_numeric($data['rating']) ? (float) $data['rating'] : null,
            userRatingsTotal: isset($data['user_ratings_total']) && is_numeric($data['user_ratings_total']) ? (int) $data['user_ratings_total'] : null,
            latitude: is_array($location) && isset($location['lat']) && is_numeric($location['lat']) ? (float) $location['lat'] : null,
            longitude: is_array($location) && isset($location['lng']) && is_numeric($location['lng']) ? (float) $location['lng'] : null,
        );
    }
}
