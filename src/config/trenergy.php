<?php
declare(strict_types=1);

return [
    'base-url' => env('TRENERGY_API_URL'),
    'api-key' => env('TRENERGY_API_KEY'),
    'headers' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'Service-lang' =>  env('TRENERGY_API_LANG')
    ]
];
