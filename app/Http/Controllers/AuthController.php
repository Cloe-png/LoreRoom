<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserLog;
use App\Support\PasswordRules;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('manage.index');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
        $credentials['email'] = User::normalizeEmail((string) $credentials['email']);
        $user = User::findByEmail($credentials['email']);

        $remember = $request->boolean('remember');

        if ($user && $user->locked_at) {
            UserLog::logAction((int) $user->id, 'account_locked', 'login', 'login');

            throw ValidationException::withMessages([
                'email' => 'Ce compte est bloque. Veuillez contacter loreroomapp@gmail.com.',
            ]);
        }

        if (!$user || !Hash::check((string) $credentials['password'], (string) $user->password)) {
            $this->registerFailedLogin($user);

            if ($user && $user->fresh() && $user->locked_at) {
                throw ValidationException::withMessages([
                    'email' => 'Ce compte est bloque. Veuillez contacter loreroomapp@gmail.com.',
                ]);
            }

            throw ValidationException::withMessages([
                'email' => 'Email ou mot de passe incorrect.',
            ]);
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();
        $this->clearFailedLoginState(Auth::user());
        $this->issueTemporaryLoginToken($request);
        UserLog::logAction((int) Auth::id(), 'connexion');

        return redirect()->intended(route('manage.index'));
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('manage.index');
        }

        return view('register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'email',
                'max:180',
                function (string $attribute, $value, \Closure $fail): void {
                    $email = User::normalizeEmail((string) $value);
                    $exists = User::supportsEmailHash()
                        ? User::query()->where('email_hash', User::emailHash($email))->exists()
                        : User::query()->where('email', $email)->exists();

                    if ($exists) {
                        $fail('Cette adresse e-mail est déjà utilisée.');
                    }
                },
            ],
            'password' => PasswordRules::defaultsWithConfirmation(),
        ], [
            'password.min' => 'Le mot de passe doit contenir au moins 12 caracteres.',
            'password.regex' => 'Le mot de passe doit contenir une majuscule, une minuscule, un chiffre et un caractere special.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $user = User::create([
            'name' => trim((string) $data['name']),
            'email' => User::normalizeEmail((string) $data['email']),
            'password' => Hash::make((string) $data['password']),
            'role' => 'utilisateur',
            'current_world_id' => null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $this->issueTemporaryLoginToken($request);
        UserLog::logAction((int) $user->id, 'connexion');

        return redirect()->route('manage.index');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            UserLog::logAction((int) $user->id, 'déconnexion');
            $user->forceFill([
                'login_token_hash' => null,
                'login_token_expires_at' => null,
            ])->save();
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Vous êtes déconnecté.');
    }

    private function issueTemporaryLoginToken(Request $request): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $plainToken = Str::random(64);
        $user->forceFill([
            'login_token_hash' => hash('sha256', $plainToken),
            'login_token_expires_at' => Carbon::now()->addHours(8),
        ])->save();

        $request->session()->put('login_temp_token', $plainToken);
    }

    private function registerFailedLogin(?User $user): void
    {
        if (!$user) {
            return;
        }

        $attempts = (int) $user->failed_login_attempts + 1;

        $user->forceFill([
            'failed_login_attempts' => $attempts,
            'last_failed_login_at' => Carbon::now(),
            'locked_at' => $attempts >= 5 ? Carbon::now() : null,
        ])->save();

        UserLog::logAction((int) $user->id, 'failed_login', 'login', 'login');

        if ($attempts >= 5) {
            UserLog::logAction((int) $user->id, 'account_locked', 'login', 'login');
        }
    }

    private function clearFailedLoginState(?User $user): void
    {
        if (!$user) {
            return;
        }

        $user->forceFill([
            'failed_login_attempts' => 0,
            'last_failed_login_at' => null,
            'locked_at' => null,
            'password_reset_pending_at' => null,
        ])->save();
    }
}
