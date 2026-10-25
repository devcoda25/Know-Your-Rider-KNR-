<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;

class JwtIssuer
{
    /** @return array{token: string, expires_at: string} */
    public function issue(User $user): array
    {
        $now = now();
        $expires = $now->copy()->addMinutes((int) config('jwt.ttl_minutes'));

        $roles = $user->activeAssignments()->map(fn ($a) => [
            'role' => $a->role->key,
            'scope_type' => $a->scope_type,
            'scope_id' => $a->scope_id,
        ])->values()->all();

        $payload = [
            'iss' => config('jwt.issuer'),
            'sub' => $user->id,
            'iat' => $now->timestamp,
            'exp' => $expires->timestamp,
            'roles' => $roles,
        ];

        return [
            'token' => JWT::encode($payload, (string) config('jwt.secret'), 'HS256'),
            'expires_at' => $expires->toIso8601String(),
        ];
    }
}
