<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'region_id' => 'nullable|exists:regions,id',
            'fcm_token' => 'nullable|string|max:2048',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'region_id' => $validated['region_id'] ?? null,
            'email_verified_at' => now(),
            'fcm_token' => $this->normalizeFcmToken($validated['fcm_token'] ?? null),
        ]);

        event(new Registered($user));

        return response()->json([
            'message' => __('api.auth_registered'),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'fcm_token' => 'nullable|string|max:2048',
        ]);

        if (! Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => [__('api.auth_invalid_credentials')],
            ]);
        }

        $user = Auth::user();

        if (! $user->hasVerifiedEmail()) {
            Auth::logout();

            return response()->json([
                'message' => __('api.auth_verify_email_first'),
            ], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        $this->persistFcmToken($user, $request->input('fcm_token'));

        return response()->json([
            'message' => __('api.auth_login_success'),
            'token' => $token,
            'user' => $this->formatUser($user->fresh()),
        ]);
    }

    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|exists:users,phone',
        ]);

        $user = User::where('phone', $request->phone)->firstOrFail();

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        return response()->json([
            'message' => __('api.auth_otp_sent'),
            '_debug_otp' => $otp,
        ]);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|exists:users,phone',
            'otp' => 'required|string|size:6',
            'fcm_token' => 'nullable|string|max:2048',
        ]);

        $user = User::where('phone', $request->phone)->firstOrFail();

        if (! $user->isOtpValid() || $user->otp !== $request->otp) {
            return response()->json([
                'message' => __('api.auth_otp_invalid'),
            ], 422);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        $user->update(['otp' => null, 'otp_expires_at' => null]);

        $token = $user->createToken('otp-token')->plainTextToken;

        $this->persistFcmToken($user, $request->input('fcm_token'));

        return response()->json([
            'message' => __('api.auth_otp_verified'),
            'token' => $token,
            'user' => $this->formatUser($user->fresh()),
        ]);
    }

    public function updateFcmToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fcm_token' => 'sometimes|nullable|string|max:2048',
        ]);

        if (array_key_exists('fcm_token', $validated)) {
            $request->user()->update([
                'fcm_token' => $this->normalizeFcmToken($validated['fcm_token']),
            ]);
        }

        return response()->json(['message' => __('api.fcm_token_saved')]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->update(['fcm_token' => null]);
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => __('api.auth_logout_success')]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('region.city.governorate.country');

        return response()->json(['data' => $this->formatUser($user)]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'required|string|max:20',
            'region_id' => 'required|exists:regions,id',
        ]);

        $user->update($validated);

        $user->load('region.city.governorate.country');

        return response()->json([
            'message' => __('api.auth_profile_updated'),
            'data' => $this->formatUser($user->fresh()),
        ]);
    }

    private function normalizeFcmToken(mixed $token): ?string
    {
        if (! is_string($token)) {
            return null;
        }

        $token = trim($token);

        return $token === '' ? null : $token;
    }

    private function persistFcmToken(User $user, mixed $token): void
    {
        $normalized = $this->normalizeFcmToken($token);
        if ($normalized === null) {
            return;
        }

        $user->forceFill(['fcm_token' => $normalized])->save();
    }

    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'email_verified' => $user->hasVerifiedEmail(),
            'created_at' => $user->created_at?->toIso8601String(),
            'region' => $user->region ? [
                'id' => $user->region->id,
                'name' => $user->region->name,
                'city_id' => $user->region->city_id,
                'city' => $user->region->city?->name,
                'governorate_id' => $user->region->city?->governorate_id,
                'governorate' => $user->region->city?->governorate?->name,
                'country_id' => $user->region->city?->governorate?->country_id,
                'country' => $user->region->city?->governorate?->country?->name,
            ] : null,
        ];
    }
}
