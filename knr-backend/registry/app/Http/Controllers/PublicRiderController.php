<?php

namespace App\Http\Controllers;

use App\Http\Resources\RiderFullResource;
use App\Http\Resources\RiderPublicResource;
use App\Models\Bike;
use App\Models\Incident;
use App\Models\Rider;
use App\Models\ScanLog;
use App\Support\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Passenger and authority lookups by QR token or number plate.
 * The same URL serves everyone; the amount of detail depends on the caller's roles.
 */
class PublicRiderController extends Controller
{
    private const WITH = ['stage', 'bike', 'documents', 'incidents'];

    public function byQr(Request $request, string $token): JsonResponse
    {
        $rider = Rider::with(self::WITH)->where('qr_token', $token)->first();

        return $this->respond($request, 'qr', $token, $rider);
    }

    public function byPlate(Request $request, string $plate): JsonResponse
    {
        $normalized = Bike::normalizePlate($plate); // exact match only, never partial

        $rider = Rider::with(self::WITH)
            ->whereHas('bike', fn ($q) => $q->where('plate_normalized', $normalized))
            ->first();

        return $this->respond($request, 'plate', $normalized, $rider);
    }

    /** Passenger report from the scan page, no login. */
    public function report(Request $request, string $token): JsonResponse
    {
        $rider = Rider::where('qr_token', $token)->firstOrFail();

        $data = $request->validate([
            'type' => ['required', 'in:reckless_riding,overcharging,harassment,other'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        Incident::create([...$data, 'rider_id' => $rider->id, 'reporter_type' => 'passenger']);

        return response()->json(['message' => 'Report received. Thank you.'], 201);
    }

    private function respond(Request $request, string $method, string $query, ?Rider $rider): JsonResponse
    {
        $user = CurrentUser::fromRequest($request);

        // Every lookup is logged; authority lookups are what riders and admins can audit.
        ScanLog::record($request, $user, $method, $query, $rider);

        if (! $rider) {
            return response()->json([
                'registered' => false,
                // Never imply the rider is illegal: many riders are simply not on KNR yet.
                'message' => 'This plate is not registered on KNR. This does not mean the rider is illegal.',
            ], 404);
        }

        $resource = $user->canViewFull($rider)
            ? new RiderFullResource($rider)
            : new RiderPublicResource($rider);

        return $resource->response();
    }
}
