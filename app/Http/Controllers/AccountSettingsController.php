<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use App\Support\PasswordRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountSettingsController extends Controller
{
    public function edit(Request $request): View
    {
        return view('manage.account.edit', [
            'user' => $request->user(),
            'passwordHelp' => PasswordRules::helpText(),
            'worldsCount' => $request->user() ? $request->user()->worlds()->count() : 0,
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => PasswordRules::defaultsWithConfirmation(),
        ], [
            'password.min' => 'Le mot de passe doit contenir au moins 12 caractères.',
            'password.regex' => 'Le mot de passe doit contenir une majuscule, une minuscule, un chiffre et un caractère special.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        if (!$user || !Hash::check((string) $data['current_password'], (string) $user->password)) {
            return back()->withErrors([
                'current_password' => 'Le mot de passe actuel est incorrect.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make((string) $data['password']),
            'failed_login_attempts' => 0,
            'last_failed_login_at' => null,
            'locked_at' => null,
            'password_reset_pending_at' => null,
            'login_token_hash' => null,
            'login_token_expires_at' => null,
            'remember_token' => null,
        ])->save();

        UserLog::logAction((int) $user->id, 'password_changed', 'manage.account.password', 'self');

        return redirect()
            ->route('manage.account.edit')
            ->with('success', 'Mot de passe mis à jour avec succès.');
    }
}
