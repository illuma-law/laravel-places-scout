<?php

declare(strict_types=1);

namespace IllumaLaw\PlacesScout;

use IllumaLaw\PlacesScout\DTOs\PlaceDetails;
use IllumaLaw\PlacesScout\DTOs\PlaceSearchResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service class for interacting with the Google Places API.
 */
final class PlacesScoutService
{
    protected string $baseUrl = 'https://maps.googleapis.com/maps/api/place';

    public function __construct(
        protected ?string $apiKey = null
    ) {}

    public function withApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function textSearch(string $query, ?string $pageToken = null): ?PlaceSearchResponse
    {
        $params = [
            'query' => $query,
            'key'   => $this->apiKey,
        ];

        if ($pageToken) {
            $params['pagetoken'] = $pageToken;
        }

        $response = Http::get("{$this->baseUrl}/textsearch/json", $params);

        if ($response->failed()) {
            Log::error('Google Places API textSearch failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
                'query'  => $query,
            ]);

            return null;
        }

        $data = $response->json();

        if (! is_array($data)) {
            return null;
        }

        return PlaceSearchResponse::fromArray($data);
    }

    public function getPlaceDetails(string $placeId): ?PlaceDetails
    {
        $response = Http::get("{$this->baseUrl}/details/json", [
            'place_id' => $placeId,
            'fields'   => 'name,formatted_address,formatted_phone_number,website,rating,user_ratings_total,geometry',
            'key'      => $this->apiKey,
        ]);

        if ($response->failed() || ! isset($response->json()['result'])) {
            Log::error('Google Places API getPlaceDetails failed', [
                'status'   => $response->status(),
                'body'     => $response->body(),
                'place_id' => $placeId,
            ]);

            return null;
        }

        return PlaceDetails::fromArray($response->json()['result']);
    }
}
