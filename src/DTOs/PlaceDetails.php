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
     * @param  array<string, mixed>  $data
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

    /**
     * Build a PlaceDetails from a search result, mapping available fields.
     * Phone number and website are not available in search results and will be null.
     */
    public static function fromSearchResult(PlaceSearchResult $result): self
    {
        return new self(
            name: $result->name,
            formattedAddress: $result->formattedAddress,
            rating: $result->rating,
            userRatingsTotal: $result->userRatingsTotal,
            latitude: $result->latitude,
            longitude: $result->longitude,
        );
    }

    /**
     * Return a new instance with null fields filled from the search result fallback.
     */
    public function mergeWith(PlaceSearchResult $fallback): static
    {
        return new self(
            name: $this->name !== '' ? $this->name : $fallback->name,
            formattedAddress: $this->formattedAddress ?? $fallback->formattedAddress,
            phoneNumber: $this->phoneNumber,
            website: $this->website,
            rating: $this->rating ?? $fallback->rating,
            userRatingsTotal: $this->userRatingsTotal ?? $fallback->userRatingsTotal,
            latitude: $this->latitude ?? $fallback->latitude,
            longitude: $this->longitude ?? $fallback->longitude,
        );
    }

    /**
     * Return a snake_case array suitable for Eloquent fill or updateOrCreate.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'formatted_address' => $this->formattedAddress,
            'phone_number' => $this->phoneNumber,
            'website' => $this->website,
            'rating' => $this->rating,
            'user_ratings_total' => $this->userRatingsTotal,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}
