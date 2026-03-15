<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * User registration.
     * Maps: auth/register.php
     * POST: fullname, email, password
     * Supports both form and JSON requests
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'email.unique' => 'Email đã tồn tại!',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu nhập lại không khớp.',
        ]);

        // Generate unique user_id
        do {
            $rand = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            $user_id = 'PKA'.$rand;
            $exists = User::where('user_id', $user_id)->exists();
        } while ($exists);

        try {
            $user = User::create([
                'user_id' => $user_id,
                'fullname' => $validated['fullname'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'reset_token' => '',
                'reset_expire' => null,
            ]);
            \Illuminate\Support\Facades\Log::info('User registered successfully', ['user_id' => $user->user_id, 'email' => $user->email]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Registration failed', ['error' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đăng ký thất bại: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->route('home')->with('error', 'Đăng ký thất bại.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công! Vui lòng đăng nhập.',
                'action' => 'show_login'
            ]);
        }

        return redirect()->route('home')->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
    }
}
