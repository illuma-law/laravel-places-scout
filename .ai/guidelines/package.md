---
description: Fluent Google Places API client for Laravel with strictly typed DTOs and multi-tenant key support
---

# laravel-places-scout

Fluent abstraction over the Google Maps Places API. Maps API responses into fully typed PHP DTOs with IDE autocompletion.

## Namespace

`IllumaLaw\PlacesScout`

## Key Classes & Facades

- `PlacesScout` facade — primary entry point
- `PlaceResult` DTO — full place details
- `PlaceSearchResult` DTO — search result item with pagination

## Config

Publish: `php artisan vendor:publish --tag="places-scout-config"`

```env
GOOGLE_PLACES_API_KEY=your_api_key_here
```

## Text Search

```php
use IllumaLaw\PlacesScout\Facades\PlacesScout;

$results = PlacesScout::search('coffee shops in Lisbon');
// returns PlaceSearchResult[]

foreach ($results as $place) {
    echo $place->name;
    echo $place->placeId;
    echo $place->formattedAddress;
}

// With pagination
$page2 = PlacesScout::search('coffee shops in Lisbon')->nextPage();
```

## Place Details

```php
$details = PlacesScout::details('ChIJ...placeId...');
// returns ?PlaceResult

if ($details) {
    echo $details->name;
    echo $details->phoneNumber;
    echo $details->website;
    echo $details->latitude;
    echo $details->longitude;
}
```

## Multi-Tenant Key Override

```php
PlacesScout::withKey($team->google_places_api_key)->search('lawyers in Porto');
```

## Error Handling

Failed API requests return `null` (for `details()`) or an empty collection (for `search()`). Exceptions are suppressed and logged — the application will not crash due to third-party instability.
