# illuma-law/laravel-places-scout

Interacting with Google Places API using strongly typed DTOs.

## Usage

### Text Search

```php
use IllumaLaw\PlacesScout\Facades\PlacesScout;

$response = PlacesScout::textSearch('Law firms in New York');

foreach ($response->results as $result) {
    // $result->name, $result->placeId, $result->formattedAddress, etc.
}

// Pagination
if ($response->nextPageToken) {
    $nextPage = PlacesScout::textSearch('query', $response->nextPageToken);
}
```

### Place Details

```php
$details = PlacesScout::getPlaceDetails('place_id_here');
// $details->phoneNumber, $details->website, $details->rating, etc.
```

### Dynamic API Key

```php
$response = PlacesScout::withApiKey('temporary-key')->textSearch('query');
```

## DTOs

- **PlaceSearchResponse**: `results` (array), `nextPageToken` (string|null).
- **PlaceSearchResult**: `placeId`, `name`, `formattedAddress`, `latitude`, `longitude`.
- **PlaceDetails**: `name`, `formattedAddress`, `phoneNumber`, `website`, etc.

## Configuration

Publish config: `php artisan vendor:publish --tag="places-scout-config"`

Options in `config/places-scout.php`:
- `api_key`: `env('GOOGLE_PLACES_API_KEY')`
