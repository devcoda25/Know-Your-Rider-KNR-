<?php

namespace App\Http\Controllers;

use App\Models\OtpCode;
use App\Models\User;
use App\Services\JwtIssuer;
use App\Services\Sms\SmsSender;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OtpController extends Controller
{
    private const PHONE_RULE = ['required', 'regex:/^\+256\d{9}$/'];

    public function request(Request $request, SmsSender $sms): JsonResponse
    {
        $data = $request->validate(['phone' => self::PHONE_RULE]);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $ttl = (int) config('otp.ttl_minutes');

        // Invalidate any earlier unused codes for this number.
        OtpCode::where('phone', $data['phone'])->whereNull('consumed_at')->update(['consumed_at' => now()]);

        OtpCode::create([
            'phone' => $data['phone'],
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes($ttl),
        ]);

        $sms->send($data['phone'], "Your KNR code is {$code}. It expires in {$ttl} minutes.");

        return response()->json(array_filter([
            'message' => 'Code sent.',
            'expires_in_seconds' => $ttl * 60,
            'debug_code' => config('otp.debug_return') ? $code : null,
        ]));
    }

    public function verify(Request $request, JwtIssuer $jwt): JsonResponse
    {
        $data = $request->validate([
            'phone' => self::PHONE_RULE,
            'code' => ['required', 'digits:6'],
        ]);

        $otp = OtpCode::where('phone', $data['phone'])
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();

        if (! $otp || $otp->attempts >= (int) config('otp.max_attempts')) {
            return response()->json(['message' => 'Code expired. Request a new one.'], 422);
        }

        $otp->increment('attempts');

        if (! Hash::check($data['code'], $otp->code_hash)) {
            return response()->json(['message' => 'Incorrect code.'], 422);
        }

        $otp->update(['consumed_at' => now()]);

        $user = User::firstOrCreate(['phone' => $data['phone']]);

        if ($user->status === 'suspended') {
            return response()->json(['message' => 'This account is suspended.'], 403);
        }

        $user->forceFill([
            'phone_verified_at' => $user->phone_verified_at ?? now(),
            'last_login_at' => now(),
        ])->save();

        return response()->json([
            ...$jwt->issue($user),
            'user' => $this->userPayload($user),
            'has_pin' => $user->pin_hash !== null,
        ]);
    }

    private function userPayload(User $user): array
    {
        return $user->only(['id', 'name', 'phone', 'status']);
    }
}
