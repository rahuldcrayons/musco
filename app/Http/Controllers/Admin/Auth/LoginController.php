<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Attempt login against users table via admin guard
        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('admin')->user();

            // Check if this user actually has an admin record
            $isAdmin = Admin::where('user_id', $user->id)->where('is_active', true)->exists();

            if (!$isAdmin) {
                // Customer tried to use admin portal — kick them out
                Auth::guard('admin')->logout();
                return redirect()->route('admin.login')
                    ->with('toast_error', 'Customer accounts must sign in at /login')
                    ->withInput($request->only('email'));
            }

            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        // Wrong credentials entirely
        return redirect()->route('admin.login')
            ->with('toast_error', 'Invalid email or password. Please try again.')
            ->withInput($request->only('email'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
