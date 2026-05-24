<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Login com Google ainda nao foi configurado.']);
        }

        $state = Str::random(40);

        session(['google_oauth_state' => $state]);

        return redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => $this->redirectUri(),
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'state' => $state,
            'prompt' => 'select_account',
        ]));
    }

    public function callback(Request $request): RedirectResponse
    {
        if ($request->has('error')) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Nao foi possivel entrar com o Google. Tente novamente.']);
        }

        if (! $request->filled('code') || ! hash_equals((string) session('google_oauth_state'), (string) $request->state)) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'A sessao do login com Google expirou. Tente novamente.']);
        }

        $request->session()->forget('google_oauth_state');

        $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'code' => $request->code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUri(),
        ]);

        if ($tokenResponse->failed() || ! $tokenResponse->json('access_token')) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Nao foi possivel validar sua conta Google.']);
        }

        $googleUser = Http::withToken($tokenResponse->json('access_token'))
            ->get('https://openidconnect.googleapis.com/v1/userinfo');

        if ($googleUser->failed() || ! $googleUser->json('email')) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Sua conta Google nao retornou um e-mail valido.']);
        }

        $user = User::where('google_id', $googleUser->json('sub'))
            ->orWhere('email', $googleUser->json('email'))
            ->first();

        if ($user) {
            $user->forceFill([
                'google_id' => $googleUser->json('sub'),
                'google_avatar' => $googleUser->json('picture'),
                'email_verified_at' => $user->email_verified_at ?: now(),
            ])->save();
        } else {
            [$name, $lastName] = $this->splitName($googleUser->json('name') ?: 'Cliente Shopla');

            $user = User::create([
                'name' => $name,
                'last_name' => $lastName,
                'email' => $googleUser->json('email'),
                'email_verified_at' => now(),
                'google_id' => $googleUser->json('sub'),
                'google_avatar' => $googleUser->json('picture'),
                'plan' => 'free',
                'plan_started_at' => now(),
                'password' => Str::password(32),
            ]);
        }

        Auth::login($user, true);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    private function redirectUri(): string
    {
        return config('services.google.redirect') ?: route('auth.google.callback');
    }

    /**
     * @return array{0: string, 1: string|null}
     */
    private function splitName(string $fullName): array
    {
        $parts = preg_split('/\s+/', trim($fullName), 2);

        return [
            $parts[0] ?: 'Cliente',
            $parts[1] ?? null,
        ];
    }
}
