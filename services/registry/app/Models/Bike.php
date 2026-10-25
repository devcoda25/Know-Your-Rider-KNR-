<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bike extends Model
{
    use HasUlids;

    protected $fillable = ['rider_id', 'plate', 'make', 'model', 'color', 'photo_path', 'stolen_at'];

    protected function casts(): array
    {
        return ['stolen_at' => 'datetime'];
    }

    /** "ubk 482-x" -> "UBK482X": spaces, dashes and case never matter in search. */
    public static function normalizePlate(string $plate): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $plate));
    }

    protected function plate(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => [
                'plate' => strtoupper(trim($value)),
                'plate_normalized' => self::normalizePlate($value),
            ],
        );
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }
}
