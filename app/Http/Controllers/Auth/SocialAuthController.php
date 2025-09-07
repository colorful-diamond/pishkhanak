<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirectToProvider($provider)
    {
        if($provider == 'google_oauth2'){
            $scopes = [
                'https://www.googleapis.com/auth/youtubepartner',
                'https://www.googleapis.com/auth/plus.me',
                'https://www.googleapis.com/auth/plus.profile.emails.read'
            ];
            return Socialite::driver($provider)->
            scopes($scopes)->
            with(['prompt' => 'consent', 'access_type' => 'offline'])->
            redirect();
        }else{
            return Socialite::driver($provider)->redirect();
        }
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Exception $e) {
            return redirect()->route('app.auth.login')->with('error', 'Unable to login using ' . $provider . '. Please try again.');
        }

        $user = User::where('email', $socialUser->getEmail())->first();

        $socialTokens = $user->social_tokens ?? [];

        $socialTokens[$provider] = [
            'token' => $socialUser->token,
            'refresh_token' => $socialUser->refreshToken,
            'expires_in' => $socialUser->expiresIn,
        ];

        if (!$user) {
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'password' => bcrypt(Str::random(16)),
                $provider . '_id' => $socialUser->getId(),
                'social_tokens' => $socialTokens,
                'email_verified_at' => now(),
            ]);
        } else {
            $user->update([
                $provider . '_id' => $socialUser->getId(),
                'social_tokens' => $socialTokens,
            ]);
        }

        Auth::login($user, true);

        return redirect()->intended('/user/dashboard')->with('status', 'Logged in successfully with ' . ucfirst($provider));
    }
}