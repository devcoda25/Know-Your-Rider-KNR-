<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verifies the bearer token issued by the auth service and stores the caller's
 * identity on the request. The gateway then forwards that identity to upstream
 * services as trusted headers (clients can never set those headers themselves).
 */
class VerifyJwt
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Missing bearer token.'], 401);
        }

        try {
            $claims = JWT::decode($token, new Key((string) config('gateway.jwt_secret'), 'HS256'));
        } catch (\Throwable) {
            return response()->json(['message' => 'Invalid or expired token.'], 401);
        }

        $request->attributes->set('gateway.user', [
            'id' => (string) $claims->sub,
            'roles' => $claims->roles ?? [],
        ]);

        return $next($request);
    }
}
