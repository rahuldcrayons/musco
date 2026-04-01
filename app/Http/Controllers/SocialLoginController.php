<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSocialAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            Log::warning("Social login failed [{$provider}]: " . $e->getMessage());
            return redirect()->route('login')->with('error', 'Social login failed. Please try again.');
        }

        // Check if social account already linked
        $socialAccount = UserSocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($socialAccount) {
            Auth::login($socialAccount->user);
            return redirect()->intended(route('account.dashboard'));
        }

        // Check if email already registered
        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            // Link social account to existing user
            $user->socialAccounts()->create([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'access_token' => $socialUser->token,
                'refresh_token' => $socialUser->refreshToken,
            ]);

            Auth::login($user);
            return redirect()->intended(route('account.dashboard'));
        }

        // Create new user
        $nameParts = explode(' ', $socialUser->getName() ?? '', 2);
        $user = User::create([
            'first_name' => $nameParts[0] ?? 'User',
            'last_name' => $nameParts[1] ?? '',
            'email' => $socialUser->getEmail(),
            'password' => bcrypt(str()->random(24)),
            'email_verified_at' => now(),
            'avatar_url' => $socialUser->getAvatar(),
        ]);

        $user->socialAccounts()->create([
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'access_token' => $socialUser->token,
            'refresh_token' => $socialUser->refreshToken,
        ]);

        Auth::login($user);

        return redirect()->route('account.dashboard')->with('success', 'Welcome to MusCo!');
    }

    private function validateProvider(string $provider): void
    {
        abort_unless(in_array($provider, ['google', 'facebook']), 404);
    }
}
