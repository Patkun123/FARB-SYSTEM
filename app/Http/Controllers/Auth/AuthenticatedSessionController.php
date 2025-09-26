<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate input
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // Detect if input is email or username
        $fieldType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        // Attempt login
        if (Auth::attempt([$fieldType => $request->login, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirect by role
            return redirect()->intended(match (auth()->user()->role) {
                'admin' => route('admin.dashboard'),
                'billing_clerk' => route('billing.dashboard'),
                'receivable_clerk' => route('receivable.dashboard'),
                default => route('dashboard'),
            });
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
