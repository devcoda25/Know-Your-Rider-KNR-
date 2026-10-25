<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * For public routes (QR scan, plate search). Passengers send no token and get the
 * public view. If a valid token is present (police, stage staff), their identity is
 * forwarded so the registry can return the full profile. A bad token is treated as
 * a guest, never as an error.
 */
class OptionalJwt
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($token = $request->bearerToken()) {
            try {
                $claims = JWT::decode($token, new Key((string) config('gateway.jwt_secret'), 'HS256'));
                $request->attributes->set('gateway.user', [
                    'id' => (string) $claims->sub,
                    'roles' => $claims->roles ?? [],
                ]);
            } catch (\Throwable) {
                // guest
            }
        }

        return $next($request);
    }
}
