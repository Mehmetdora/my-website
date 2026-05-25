<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $allowedEmails = collect(config('admin_security.allowed_emails', []))
            ->map(fn ($email) => strtolower((string) $email))
            ->filter();

        if (! $user || ! $allowedEmails->contains(strtolower((string) $user->email))) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            abort(403, 'You are not authorized to access this admin area.');
        }

        return $next($request);
    }
}
