<?php

/**
 * Global API URL Resolver
 */
function api_url(string $key = 'complaint'): string
{
    $app = config('App');

    return match ($key) {
        'capacity' => $app->capacityUrl,
        'complaint' => $app->complaintUrl,
        default     => '',
    };
}
