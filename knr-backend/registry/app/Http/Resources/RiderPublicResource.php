<?php

namespace App\Http\Resources;

use App\Models\Rider;
use App\Support\Photos;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** What a passenger (no login) sees after scanning a QR code or searching a plate. */
class RiderPublicResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        /** @var Rider $rider */
        $rider = $this->resource;
        $status = $rider->complianceStatus();

        // A flagged rider or bike shows only a warning, nothing else.
        if (in_array($status, ['suspended', 'stolen'], true)) {
            return [
                'registered' => true,
                'status' => $status,
                'plate' => $rider->bike?->plate,
                'warning' => 'This rider or bike has been flagged. Do not ride. Report it or call the police.',
            ];
        }

        return [
            'registered' => true,
            'status' => $status, // verified | expiring_soon | expired | incomplete
            'rider' => [
                'full_name' => $rider->full_name,
                'photo_url' => Photos::url($rider->photo_path),
            ],
            'bike' => [
                'plate' => $rider->bike?->plate,
                'make' => $rider->bike?->make,
                'model' => $rider->bike?->model,
                'color' => $rider->bike?->color,
                'photo_url' => Photos::url($rider->bike?->photo_path),
            ],
            'stage' => [
                'name' => $rider->stage->name,
                'district' => $rider->stage->district,
                'division' => $rider->stage->division,
                'parish' => $rider->stage->parish,
                'contact_name' => $rider->stage->contact_name,
                'contact_phone' => $rider->stage->contact_phone,
            ],
            'permit_valid_until' => $rider->validUntil('permit'),
            'insurance_valid_until' => $rider->validUntil('insurance'),
        ];
    }
}
