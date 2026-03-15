<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    /**
     * Show forgot password form (GET).
     * For modal/form that posts to sendResetLink.
     */
    public function showForgotForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send reset link (POST email).
     * Maps: auth/send_reset_link.php
     * Uses users.reset_token and users.reset_expire (same as plain PHP).
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->with('error', 'Email không tồn tại!');
        }

        $token = Str::random(32);
        $expire = now()->addHour();

        $user->update([
            'reset_token' => $token,
            'reset_expire' => $expire,
        ]);

        $link = url('/password/reset/'.$token);

        $subject = 'Khôi phục mật khẩu TicketBox';
        $messageHtml = "
            <h3>Yêu cầu khôi phục mật khẩu</h3>
            <p>Nhấn vào liên kết sau để đặt lại mật khẩu (hiệu lực trong 1 giờ):</p>
            <a href='{$link}'>{$link}</a>
        ";

        try {
            \Illuminate\Support\Facades\Mail::html($messageHtml, function ($message) use ($user, $subject) {
                $message->to($user->email)
                        ->subject($subject);
            });
            return redirect()->route('home')->with('success', 'Liên kết khôi phục đã được gửi!');
        } catch (\Exception $e) {
            \Log::error('Password reset email failed: ' . $e->getMessage());
            return back()->with('error', 'Gửi email thất bại!');
        }
    }

    /**
     * Show reset password form (GET with token).
     * Maps: auth/reset_password.php (GET)
     */
    public function showResetForm(Request $request, string $token): View|RedirectResponse
    {
        $user = User::where('reset_token', $token)->first();

        if (! $user || ! $user->reset_expire || $user->reset_expire->isPast()) {
            return redirect()->route('home')->with('error', 'Liên kết không hợp lệ hoặc đã hết hạn!');
        }

        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Process password reset (POST).
     * Maps: auth/reset_password.php (POST)
     */
    public function reset(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::where('reset_token', $validated['token'])->first();

        if (! $user || ! $user->reset_expire || $user->reset_expire->isPast()) {
            return redirect()->route('home')->with('error', 'Liên kết không hợp lệ hoặc đã hết hạn!');
        }

        $user->update([
            'password' => $validated['password'],
            'reset_token' => '',
            'reset_expire' => null,
        ]);

        return redirect()->route('home')->with('success', 'Đổi mật khẩu thành công!');
    }
}
