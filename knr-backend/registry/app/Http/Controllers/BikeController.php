<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use App\Support\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BikeController extends Controller
{
    private const CAN_FLAG = ['super_admin', 'traffic_officer', 'stage_head'];

    /** Flag a bike as stolen: every lookup then shows a warning. */
    public function flagStolen(Request $request, string $bike): JsonResponse
    {
        abort_unless(CurrentUser::fromRequest($request)->hasRole(self::CAN_FLAG), 403);

        $bike = Bike::findOrFail($bike);
        $bike->update(['stolen_at' => now()]);

        return response()->json(['plate' => $bike->plate, 'stolen' => true]);
    }

    public function clearStolen(Request $request, string $bike): JsonResponse
    {
        abort_unless(CurrentUser::fromRequest($request)->hasRole(self::CAN_FLAG), 403);

        $bike = Bike::findOrFail($bike);
        $bike->update(['stolen_at' => null]);

        return response()->json(['plate' => $bike->plate, 'stolen' => false]);
    }
}
