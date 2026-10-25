<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incident extends Model
{
    use HasUlids;

    protected $fillable = ['rider_id', 'reporter_type', 'reporter_user_id', 'type', 'description', 'status'];

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }
}
