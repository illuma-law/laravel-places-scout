<?php

declare(strict_types=1);

namespace IllumaLaw\PlacesScout\Facades;

use IllumaLaw\PlacesScout\PlacesScoutService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \IllumaLaw\PlacesScout\DTOs\PlaceSearchResponse|null textSearch(string $query, ?string $pageToken = null, ?string $location = null, ?int $radius = null, ?string $type = null, ?string $language = null)
 * @method static \IllumaLaw\PlacesScout\DTOs\PlaceDetails|null getPlaceDetails(string $placeId, ?string $fields = null)
 * @method static \IllumaLaw\PlacesScout\PlacesScoutService withApiKey(string $apiKey)
 *
 * @see PlacesScoutService
 */
final class PlacesScout extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'places-scout';
    }
}
