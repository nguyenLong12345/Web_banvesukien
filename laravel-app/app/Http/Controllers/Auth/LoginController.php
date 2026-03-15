<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Legacy email/password login.
     * Maps: auth/login.php
     * POST: email, password
     * Supports both form and JSON requests
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = \App\Models\User::where('email', $validated['email'])->first();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email không tồn tại trong hệ thống.'
                ], 401);
            }
            return redirect()->route('home')->with('error', 'email');
        }

        if (! \Illuminate\Support\Facades\Hash::check($validated['password'], $user->password)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mật khẩu không đúng.'
                ], 401);
            }
            return redirect()->route('home')->with('error', 'password');
        }

        Auth::guard('web')->login($user, true);

        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập thành công!',
                'redirect' => route('home'),
                'user' => [
                    'id' => $user->user_id,
                    'name' => $user->fullname,
                    'email' => $user->email
                ]
            ]);
        }

        return redirect()->intended(route('home'));
    }

    /**
     * Logout user.
     * Maps: auth/logout.php
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
