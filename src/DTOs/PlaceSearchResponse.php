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

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        /** @var mixed $results */
        $results = $data['results'] ?? [];

        return new self(
            results: array_map(
                function (mixed $result): PlaceSearchResult {
                    /** @var array<string, mixed> $resultArr */
                    $resultArr = is_array($result) ? $result : [];

                    return PlaceSearchResult::fromArray($resultArr);
                },
                is_array($results) ? $results : []
            ),
            nextPageToken: is_string($data['next_page_token'] ?? null) ? (string) $data['next_page_token'] : null,
            status: is_string($data['status'] ?? null) ? (string) $data['status'] : null,
        );
    }
}
