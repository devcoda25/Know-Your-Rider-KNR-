<?php

namespace App\Http\Resources;

use App\Models\Rider;
use App\Support\Photos;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Verified profile for police and stage roles. Unlike the public view it always
 * shows every detail, including flagged riders, plus the restricted fields.
 */
class RiderFullResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        /** @var Rider $rider */
        $rider = $this->resource;
        $bike = $rider->bike;
        $stage = $rider->stage;

        return [
            'registered' => true,
            'id' => $rider->id,
            'status' => $rider->complianceStatus(),
            'rider_status' => $rider->status,
            'suspension_reason' => $rider->suspension_reason,
            'full_name' => $rider->full_name,
            'phone' => $rider->phone,
            'national_id_masked' => self::mask($rider->national_id),
            'photo_url' => Photos::url($rider->photo_path),
            'qr_url' => $rider->qrUrl(),
            'bike' => [
                'id' => $bike?->id,
                'plate' => $bike?->plate,
                'make' => $bike?->make,
                'model' => $bike?->model,
                'color' => $bike?->color,
                'photo_url' => Photos::url($bike?->photo_path),
                'stolen' => (bool) $bike?->stolen_at,
            ],
            'stage' => [
                'id' => $stage->id,
                'name' => $stage->name,
                'district' => $stage->district,
                'division' => $stage->division,
                'parish' => $stage->parish,
                'contact_name' => $stage->contact_name,
                'contact_phone' => $stage->contact_phone,
            ],
            'documents' => $rider->documents->map(fn ($d) => [
                'type' => $d->type,
                'number' => $d->number,
                'issuer' => $d->issuer,
                'expires_at' => $d->expires_at->toDateString(),
            ])->values(),
            'permit_valid_until' => $rider->validUntil('permit'),
            'insurance_valid_until' => $rider->validUntil('insurance'),
            'open_incidents' => $rider->incidents->where('status', 'open')->count(),
        ];
    }

    /** CM95123456K becomes CM•••••••••K */
    public static function mask(string $id): string
    {
        $len = strlen($id);

        return $len <= 3 ? str_repeat('•', $len) : substr($id, 0, 2).str_repeat('•', $len - 3).substr($id, -1);
    }
}
