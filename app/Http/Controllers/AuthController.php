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
use Illuminate\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

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
        $credentials['email'] = mb_strtolower(trim((string) $credentials['email']));
        $user = User::where('email', $credentials['email'])->first();

        $remember = $request->boolean('remember');

        if ($user && $user->locked_at) {
            UserLog::logAction((int) $user->id, 'account_locked', 'login', 'login');

            throw ValidationException::withMessages([
                'email' => __('app.auth.account_locked'),
            ]);
        }

        if (!Auth::attempt($credentials, $remember)) {
            $this->registerFailedLogin($user);

            if ($user && $user->fresh() && $user->locked_at) {
                throw ValidationException::withMessages([
                    'email' => __('app.auth.account_locked'),
                ]);
            }

            throw ValidationException::withMessages([
                'email' => __('app.auth.invalid_credentials'),
            ]);
        }

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

        if (request()->hasSession() && !request()->session()->has('locale')) {
            request()->session()->put('locale', 'en');
            app()->setLocale('en');
        }

        return view('register', [
            'roleOptions' => config('module_access.signup_roles', []),
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', 'unique:users,email'],
            'locale' => ['required', 'string', Rule::in(array_keys(LaravelLocalization::getSupportedLocales()))],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['required', 'string', Rule::in(array_keys(config('module_access.signup_roles', [])))],
            'password' => PasswordRules::defaultsWithConfirmation(),
        ], [
            'password.min' => __('app.auth.password_min'),
            'password.regex' => __('app.auth.password_regex'),
            'password.confirmed' => __('app.auth.password_confirmed'),
        ]);

        $user = User::create([
            'name' => trim((string) $data['name']),
            'email' => mb_strtolower(trim((string) $data['email'])),
            'password' => Hash::make((string) $data['password']),
            'role' => 'utilisateur',
            'roles' => array_values(array_unique(array_map('strval', $data['roles']))),
            'current_world_id' => null,
            'preferred_locale' => $data['locale'],
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('locale', $data['locale']);
        $this->issueTemporaryLoginToken($request);
        UserLog::logAction((int) $user->id, 'connexion');

        return redirect()->route('manage.index');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            UserLog::logAction((int) $user->id, 'deconnexion');
            $user->forceFill([
                'login_token_hash' => null,
                'login_token_expires_at' => null,
            ])->save();
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', __('app.auth.logged_out'));
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
