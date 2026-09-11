<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebFcmTokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fcm_token' => 'required|string|max:2048',
        ]);

        $token = trim($validated['fcm_token']);
        $user = $request->user();

        if ($user->fcm_token === $token) {
            return response()->json(['message' => __('api.fcm_token_saved')]);
        }

        $user->forceFill(['fcm_token' => $token])->save();

        return response()->json(['message' => __('api.fcm_token_saved')]);
    }
}
