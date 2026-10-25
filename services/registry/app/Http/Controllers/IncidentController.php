<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Rider;
use App\Support\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Incidents reported by signed-in officers and stage staff. Passenger reports are in PublicRiderController. */
class IncidentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $user = CurrentUser::fromRequest($request);
        abort_unless($user->hasRole(['super_admin', 'traffic_officer', 'lc_official', 'stage_head', 'stage_official']), 403);

        $data = $request->validate([
            'rider_id' => ['required', 'string', 'exists:riders,id'],
            'type' => ['required', 'in:reckless_riding,overcharging,harassment,offence,other'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $reporterType = $user->hasRole('traffic_officer') ? 'police' : ($user->hasRole('lc_official') ? 'lc' : 'stage');

        $incident = Incident::create([
            ...$data,
            'reporter_type' => $reporterType,
            'reporter_user_id' => $user->id,
        ]);

        return response()->json($incident, 201);
    }

    public function index(Request $request): JsonResponse
    {
        $user = CurrentUser::fromRequest($request);

        $incidents = Incident::query()
            ->whereIn('rider_id', Rider::visibleTo($user)->select('id'))
            ->when($request->query('rider_id'), fn ($q, $id) => $q->where('rider_id', $id))
            ->latest()
            ->paginate(25);

        return response()->json($incidents);
    }
}
