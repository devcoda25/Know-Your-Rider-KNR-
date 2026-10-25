<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\JwtIssuer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PinController extends Controller
{
    /** Set or change the PIN of the signed-in user (identity comes from the gateway). */
    public function set(Request $request): JsonResponse
    {
        $data = $request->validate(['pin' => ['required', 'digits_between:4,6']]);

        $user = User::findOrFail($request->header('X-User-Id'));
        $user->forceFill(['pin_hash' => Hash::make($data['pin'])])->save();

        return response()->json(['message' => 'PIN saved.']);
    }

    /** Returning users on a trusted device sign in with phone + PIN. */
    public function login(Request $request, JwtIssuer $jwt): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'regex:/^\+256\d{9}$/'],
            'pin' => ['required', 'digits_between:4,6'],
        ]);

        $user = User::where('phone', $data['phone'])->first();

        if (! $user || ! $user->pin_hash || ! Hash::check($data['pin'], $user->pin_hash)) {
            return response()->json(['message' => 'Phone number or PIN is incorrect.'], 422);
        }

        if ($user->status === 'suspended') {
            return response()->json(['message' => 'This account is suspended.'], 403);
        }

        $user->forceFill(['last_login_at' => now()])->save();

        return response()->json([
            ...$jwt->issue($user),
            'user' => $user->only(['id', 'name', 'phone', 'status']),
        ]);
    }
}
