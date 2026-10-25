<?php

namespace App\Models;

use App\Support\CurrentUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Rider extends Model
{
    use HasUlids;

    protected $fillable = [
        'stage_id', 'full_name', 'phone', 'national_id', 'national_id_hash', 'date_of_birth', 'address',
        'next_of_kin_name', 'next_of_kin_phone', 'photo_path', 'status', 'suspension_reason',
        'consent_at', 'registered_by',
    ];

    protected function casts(): array
    {
        return [
            'national_id' => 'encrypted',
            'date_of_birth' => 'date',
            'consent_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Rider $rider) {
            $rider->qr_token ??= Str::random(48);
        });
    }

    public static function hashNationalId(string $nationalId): string
    {
        $normalized = strtoupper(preg_replace('/\s+/', '', $nationalId));

        return hash_hmac('sha256', $normalized, (string) config('app.key'));
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    public function bike(): HasOne
    {
        return $this->hasOne(Bike::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function qrUrl(): string
    {
        return config('registry.public_scan_url').'/'.$this->qr_token;
    }

    /**
     * suspended | stolen | incomplete | expired | expiring_soon | verified
     * Always shown with an icon and text on screen, never by colour alone.
     */
    public function complianceStatus(): string
    {
        if ($this->status === 'suspended') {
            return 'suspended';
        }

        if ($this->bike?->stolen_at) {
            return 'stolen';
        }

        $docs = $this->documents->groupBy('type');
        $soon = now()->addDays((int) config('registry.expiring_soon_days'));
        $result = 'verified';

        foreach (config('registry.required_documents') as $type) {
            // The latest document of each type decides.
            $latest = $docs->get($type)?->sortByDesc('expires_at')->first();

            if (! $latest) {
                return 'incomplete';
            }

            if ($latest->expires_at->isPast()) {
                return 'expired';
            }

            if ($latest->expires_at->lte($soon)) {
                $result = 'expiring_soon';
            }
        }

        return $result;
    }

    public function validUntil(string $type): ?string
    {
        return $this->documents->where('type', $type)->sortByDesc('expires_at')->first()?->expires_at?->toDateString();
    }

    /**
     * Riders this caller may list.
     * TODO: authority roles (traffic police, LC) should be scoped by division/parish
     * through the stage's location, and org admins by organization.
     */
    public function scopeVisibleTo(Builder $query, CurrentUser $user): Builder
    {
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        $ids = $user->scopeIds('stage');

        return $ids ? $query->whereIn('stage_id', $ids) : $query->whereRaw('1 = 0');
    }
}
