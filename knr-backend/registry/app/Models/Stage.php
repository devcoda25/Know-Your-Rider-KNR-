<?php

namespace App\Models;

use App\Support\CurrentUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stage extends Model
{
    use HasUlids;

    protected $fillable = [
        'name', 'district', 'division', 'parish', 'village', 'status',
        'head_user_id', 'contact_name', 'contact_phone', 'subscription_status',
    ];

    public function riders(): HasMany
    {
        return $this->hasMany(Rider::class);
    }

    /**
     * Which stages this caller may see.
     * TODO: authorities (police, LC, municipal) should see stages inside their
     * division / parish scope; for now only super admins and own-stage roles.
     */
    public function scopeVisibleTo(Builder $query, CurrentUser $user): Builder
    {
        if ($user->hasRole(['super_admin', 'municipal_officer'])) {
            return $query;
        }

        $ids = $user->scopeIds('stage');

        return $ids ? $query->whereIn('id', $ids) : $query->whereRaw('1 = 0');
    }
}
