<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Support\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class StageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = CurrentUser::fromRequest($request);

        $stages = Stage::visibleTo($user)
            ->withCount('riders')
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->orderBy('name')
            ->paginate(25);

        return response()->json($stages);
    }

    /** Anyone signed in can apply to open a stage. It stays "pending" until approved. */
    public function store(Request $request): JsonResponse
    {
        $user = CurrentUser::fromRequest($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'district' => ['required', 'string', 'max:80'],
            'division' => ['required', 'string', 'max:80'],
            'parish' => ['nullable', 'string', 'max:80'],
            'village' => ['nullable', 'string', 'max:80'],
            'contact_name' => ['nullable', 'string', 'max:120'],
            'contact_phone' => ['nullable', 'regex:/^\+256\d{9}$/'],
        ]);

        $stage = Stage::create([...$data, 'head_user_id' => $user->id, 'status' => 'pending']);

        return response()->json($stage, 201);
    }

    public function show(Request $request, string $stage): JsonResponse
    {
        $user = CurrentUser::fromRequest($request);

        return response()->json(
            Stage::visibleTo($user)->withCount('riders')->findOrFail($stage)
        );
    }

    /**
     * Approve a stage and make its applicant the stage head.
     * The new role only appears in the user's token the next time they sign in.
     */
    public function approve(Request $request, string $stage): JsonResponse
    {
        abort_unless(CurrentUser::fromRequest($request)->hasRole(['super_admin', 'municipal_officer']), 403);

        $stage = Stage::findOrFail($stage);

        if ($stage->head_user_id) {
            try {
                $response = Http::withHeaders(['X-Internal-Key' => (string) config('registry.internal_key')])
                    ->acceptJson()
                    ->timeout(10)
                    ->post(config('registry.auth_url').'/api/internal/role-assignments', [
                        'user_id' => $stage->head_user_id,
                        'role' => 'stage_head',
                        'scope_type' => 'stage',
                        'scope_id' => $stage->id,
                    ]);
            } catch (\Throwable) {
                return response()->json(['message' => 'The auth service is unavailable. Try again.'], 503);
            }

            if ($response->failed()) {
                return response()->json(['message' => 'Could not assign the stage head role.'], 502);
            }
        }

        $stage->update(['status' => 'active']);

        return response()->json($stage);
    }

    public function suspend(Request $request, string $stage): JsonResponse
    {
        abort_unless(CurrentUser::fromRequest($request)->hasRole(['super_admin', 'municipal_officer']), 403);

        $stage = Stage::findOrFail($stage);
        $stage->update(['status' => 'suspended']);

        return response()->json($stage);
    }
}
