<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search', '');

        $query = User::query();
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('user_id', 'like', "%{$search}%")
                    ->orWhere('fullname', 'like', "%{$search}%");
            });
        }
        $users = $query->orderByDesc('user_id')->get();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|string',
            'fullname' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $user->update([
            'fullname' => $validated['fullname'],
            'email' => $validated['email'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật tài khoản thành công.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Đã xóa người dùng.');
    }
}
