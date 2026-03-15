<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show admin login form.
     * Maps: admin/login.php (GET)
     */
    public function showLoginForm(): View
    {
        return view('admin.login');
    }

    /**
     * Admin login. Guard: admin.
     * Maps: admin/login.php (POST: username, password)
     */
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        \Log::info('Admin login attempt', [
            'username' => $validated['username'],
            'session_id' => session()->getId(),
        ]);

        if (Auth::guard('admin')->attempt([
            'username' => $validated['username'],
            'password' => $validated['password'],
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();

            $admin = Auth::guard('admin')->user();
            
            // Debug: Log successful login
            \Log::info('Admin logged in successfully', [
                'username' => $validated['username'],
                'admin_id' => $admin->id,
                'session_id' => session()->getId(),
                'auth_check' => Auth::guard('admin')->check(),
                'redirect_to' => route('admin.dashboard')
            ]);

            // Thử redirect đến /admin/dashboard thay vì /admin
            return redirect()->to('/admin/dashboard');
        }

        \Log::warning('Admin login failed', [
            'username' => $validated['username']
        ]);

        return back()->withErrors(['error' => 'Sai tài khoản hoặc mật khẩu.'])->withInput($request->only('username'));
    }

    /**
     * Admin logout.
     * Maps: admin/logout.php
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
