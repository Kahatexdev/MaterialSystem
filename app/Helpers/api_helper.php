<?php

/**
 * Global API URL Resolver
 */
function api_url(string $key = 'capacity'): string
{
    $app = config('App');

    return match ($key) {
        'capacity' => $app->capacityUrl,
        default     => '',
    };
}
