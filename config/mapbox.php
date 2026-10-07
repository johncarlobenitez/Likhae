<?php

return [
    // Browser code receives only the public token. Keep any secret token out of
    // this config value and out of rendered HTML/JavaScript.
    'public_token' => env('MAPBOX_PUBLIC_TOKEN'),
    'style' => env('MAPBOX_STYLE', 'mapbox://styles/mapbox/streets-v12'),
    'default_center' => [121.0, 14.6],
    'default_zoom' => 10,
];
