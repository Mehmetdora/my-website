<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login', [
            'admin' => true,
            'noindex' => true,
            'title' => 'Admin Login',
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        if (! $this->isAllowedAdminEmail($credentials['email']) || ! Auth::attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'Email or password is incorrect.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->regenerateToken();
        $request->session()->put('admin_authenticated_at', now()->timestamp);
        $request->session()->put('admin_last_activity', now()->timestamp);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    private function isAllowedAdminEmail(string $email): bool
    {
        return collect(config('admin_security.allowed_emails', []))
            ->map(fn ($allowedEmail) => strtolower((string) $allowedEmail))
            ->contains(strtolower($email));
    }
}
