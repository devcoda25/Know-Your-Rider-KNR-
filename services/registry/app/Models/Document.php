<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasUlids;

    protected $fillable = ['rider_id', 'type', 'number', 'issuer', 'expires_at', 'file_path'];

    protected function casts(): array
    {
        return ['expires_at' => 'date'];
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }
}
