<?php

declare(strict_types=1);

namespace IllumaLaw\PlacesScout\Facades;

use IllumaLaw\PlacesScout\PlacesScoutService;
use Illuminate\Support\Facades\Facade;

/**
 * Facade for the Places Scout service.
 *
 * @method static \IllumaLaw\PlacesScout\DTOs\PlaceSearchResponse|null textSearch(string $query, ?string $pageToken = null)
 * @method static \IllumaLaw\PlacesScout\DTOs\PlaceDetails|null getPlaceDetails(string $placeId)
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
