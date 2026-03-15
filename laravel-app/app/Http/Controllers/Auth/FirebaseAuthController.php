<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FirebaseAuthController extends Controller
{
    public function __construct(
        protected FirebaseService $firebaseService
    ) {}

    /**
     * Verify Firebase idToken, create/update user, set session.
     * Maps: auth/firebase_verify.php
     * POST JSON: { "idToken": "..." }
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'idToken' => ['required', 'string'],
        ]);

        $payload = $this->firebaseService->verifyIdToken($request->input('idToken'));

        if (! $payload) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token không hợp lệ',
            ], 401);
        }

        $user = $this->firebaseService->findOrCreateUser(
            $payload['uid'],
            $payload['email'],
            $payload['fullname'] ?? null
        );

        Auth::guard('web')->login($user, true);
        $request->session()->regenerate();

        return response()->json([
            'status' => 'success',
            'user_id' => $user->user_id,
            'fullname' => $user->fullname,
        ]);
    }
}
