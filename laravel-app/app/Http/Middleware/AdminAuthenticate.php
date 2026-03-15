<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        \Log::info('AdminAuthenticate middleware called', [
            'url' => $request->url(),
            'session_id' => session()->getId(),
            'auth_check' => Auth::guard('admin')->check(),
            'admin' => Auth::guard('admin')->user() ? Auth::guard('admin')->user()->username : null
        ]);

        if (!Auth::guard('admin')->check()) {
            \Log::warning('Admin not authenticated, redirecting to login');
            return redirect()->route('admin.login')->withErrors(['error' => 'Vui lòng đăng nhập để tiếp tục.']);
        }

        \Log::info('Admin authenticated, proceeding');
        return $next($request);
    }
}
