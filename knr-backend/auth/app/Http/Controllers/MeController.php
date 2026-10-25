<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = User::with('organization')->findOrFail($request->header('X-User-Id'));

        return response()->json([
            'user' => $user->only(['id', 'name', 'phone', 'status']),
            'organization' => $user->organization?->only(['id', 'name', 'type']),
            'roles' => $user->activeAssignments()->map(fn ($a) => [
                'role' => $a->role->key,
                'scope_type' => $a->scope_type,
                'scope_id' => $a->scope_id,
            ])->values(),
        ]);
    }
}
