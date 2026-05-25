<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforceAdminSessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        $timeoutSeconds = max(1, (int) config('admin_security.idle_timeout_minutes', 45)) * 60;
        $lastActivity = (int) $request->session()->get('admin_last_activity', 0);
        $now = now()->timestamp;

        if ($lastActivity > 0 && ($now - $lastActivity) > $timeoutSeconds) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Admin session expired. Please log in again.',
                ], 401);
            }

            return redirect()
                ->route('admin.login')
                ->withErrors(['email' => 'Admin session expired. Please log in again.']);
        }

        $request->session()->put('admin_last_activity', $now);

        return $next($request);
    }
}
