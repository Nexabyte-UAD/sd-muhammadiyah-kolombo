<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminIdleTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        $timeout = max(1, (int) config('auth.admin_idle_timeout', 30)) * 60;
        $lastActivity = (int) $request->session()->get('admin_last_activity', time());

        if (time() - $lastActivity > $timeout) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('status', 'Sesi berakhir karena tidak ada aktivitas. Silakan masuk kembali.');
        }

        $request->session()->put('admin_last_activity', time());

        return $next($request);
    }
}
