<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RoleAssignment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Service-to-service only (guarded by the internal key, not exposed by the gateway). */
class InternalRoleController extends Controller
{
    public function assign(Request $request): JsonResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'string', 'exists:users,id'],
            'role' => ['required', 'string', 'exists:roles,key'],
            'scope_type' => ['nullable', 'string', 'max:30'],
            'scope_id' => ['nullable', 'string', 'max:40'],
        ]);

        $assignment = RoleAssignment::firstOrCreate([
            'user_id' => User::findOrFail($data['user_id'])->id,
            'role_id' => Role::where('key', $data['role'])->firstOrFail()->id,
            'scope_type' => $data['scope_type'] ?? null,
            'scope_id' => $data['scope_id'] ?? null,
        ], ['granted_by' => 'registry']);

        return response()->json($assignment, 201);
    }
}
