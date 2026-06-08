<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Enums\UserRole;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()->withErrors([
                'email' => __('auth.failed'),
            ])->withInput($request->except('password'));
        }

        $request->session()->regenerate();

        $user = Auth::user();

        return match ($user->role) {
            UserRole::Admin => redirect()->intended(route('admin.dashboard')),
            UserRole::HeadOfOperation => redirect()->intended(route('admin.dashboard')),
            UserRole::Manager => redirect()->intended(route('manager.dashboard')),
            UserRole::TeamMember => redirect()->intended(route('member.dashboard')),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    protected function authenticated(Request $request, $user)
    {
        // kept for compatibility; role-based redirect handled directly in login()
        return $this->login($request);
    }

}



