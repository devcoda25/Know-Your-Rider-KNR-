<?php

namespace App\Support;

class Photos
{
    /** Public URL (through the gateway) of a stored photo. */
    public static function url(?string $path): ?string
    {
        return $path ? config('registry.gateway_public_url').'/public/files/'.$path : null;
    }
}
