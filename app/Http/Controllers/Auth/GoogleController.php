<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // If user exists but doesn't have google_id, update it
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
                
                Auth::login($user, true);
            } else {
                // Create a new user
                $newUser = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt(Str::random(24)), // Random secure password
                    'role' => 'customer',
                    'email_verified_at' => now(), // Mark email as verified since it's from Google
                ]);

                Auth::login($newUser, true);
            }

            return redirect()->intended(route('account.dashboard'));

        } catch (\Exception $e) {
            // Temporarily show the exact error for debugging
            dd('GOOGLE OAUTH HATASI: ' . $e->getMessage(), $e->getTraceAsString());
            // return redirect()->route('login')->with('error', 'Google ile giriş yaparken bir hata oluştu. Lütfen tekrar deneyin.');
        }
    }
}
