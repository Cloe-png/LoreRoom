<?php

namespace App\Http\Controllers;

use App\Models\User;
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

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Email ou mot de passe incorrect.',
            ]);
        }

        $request->session()->regenerate();
        $this->issueTemporaryLoginToken($request);

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
            'email' => ['required', 'email', 'max:180', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => trim((string) $data['name']),
            'email' => mb_strtolower(trim((string) $data['email'])),
            'password' => Hash::make((string) $data['password']),
            'role' => 'utilisateur',
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $this->issueTemporaryLoginToken($request);

        return redirect()->route('manage.index');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->forceFill([
                'login_token_hash' => null,
                'login_token_expires_at' => null,
            ])->save();
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Vous etes deconnecte.');
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
}
