<?php

return [
    /**
     * Your Google Places API Key.
     */
    'api_key' => env('GOOGLE_PLACES_API_KEY'),

    /**
     * Comma-separated list of fields to request from the Place Details API.
     * See: https://developers.google.com/maps/documentation/places/web-service/details#fields
     */
    'details_fields' => env(
        'PLACES_SCOUT_DETAILS_FIELDS',
        'name,formatted_address,formatted_phone_number,website,rating,user_ratings_total,geometry'
    ),
];
