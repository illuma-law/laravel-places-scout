<?php

declare(strict_types=1);

namespace IllumaLaw\PlacesScout\DTOs;

/**
 * Data Transfer Object for Google Place Search Response.
 */
final readonly class PlaceSearchResponse
{
    /**
     * @param  array<PlaceSearchResult>  $results
     */
    public function __construct(
        public array $results,
        public ?string $nextPageToken = null,
        public ?string $status = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            results: array_map(
                fn (array $result) => PlaceSearchResult::fromArray($result),
                $data['results'] ?? []
            ),
            nextPageToken: $data['next_page_token'] ?? null,
            status: $data['status'] ?? null,
        );
    }
}
