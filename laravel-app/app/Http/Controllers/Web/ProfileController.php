<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the user profile page.
     */
    public function show(): View
    {
        return view('profile.show', [
            'user' => auth()->user()
        ]);
    }

    /**
     * Update user's full name.
     */
    public function updateInfo(Request $request)
    {
        $validated = $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
        ]);

        $user = auth()->user();
        $user->fullname = $validated['fullname'];
        $user->save();

        return redirect()->back()->with('success', 'Đã cập nhật tên hiển thị thành công!');
    }

    /**
     * Change user's password.
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        $user = auth()->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $user->password = $validated['password'];
        $user->save();

        return redirect()->back()->with('success', 'Đã đổi mật khẩu thành công!');
    }
}
