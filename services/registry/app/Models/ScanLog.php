<?php

namespace App\Models;

use App\Support\CurrentUser;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ScanLog extends Model
{
    use HasUlids;

    public const UPDATED_AT = null;

    protected $fillable = [
        'actor_user_id', 'actor_roles', 'rider_id', 'method', 'query', 'found', 'ip', 'lat', 'lng',
    ];

    protected function casts(): array
    {
        return ['actor_roles' => 'array', 'found' => 'boolean'];
    }

    public static function record(Request $request, CurrentUser $user, string $method, string $query, ?Rider $rider): self
    {
        return static::create([
            'actor_user_id' => $user->id,
            'actor_roles' => $user->isGuest() ? null : array_column($user->roles, 'role'),
            'rider_id' => $rider?->id,
            'method' => $method,
            'query' => $query,
            'found' => $rider !== null,
            'ip' => $request->header('X-Forwarded-For') ?: $request->ip(),
            'lat' => $request->query('lat'),
            'lng' => $request->query('lng'),
        ]);
    }
}
