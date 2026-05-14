<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserLog;
use App\Support\PasswordRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserAdminController extends Controller
{
    private const ROLE_OPTIONS = [
        'admin',
        'chanceux',
        'utilisateur',
    ];

    public function index(Request $request): View
    {
        $this->requireAdmin();

        $q = trim((string) $request->query('q', ''));

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . $q . '%';
                $query->where(function ($sub) use ($like) {
                    $sub->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('role', 'like', $like);
                });
            })
            ->orderByDesc('locked_at')
            ->orderByDesc('password_reset_pending_at')
            ->orderBy('name')
            ->orderBy('email')
            ->paginate(15)
            ->appends(['q' => $q]);

        return view('manage.users.index', [
            'users' => $users,
            'q' => $q,
            'roleOptions' => self::ROLE_OPTIONS,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->requireAdmin();

        $data = $request->validate([
            'role' => ['required', Rule::in(self::ROLE_OPTIONS)],
        ]);

        $user->update([
            'role' => $data['role'],
        ]);

        return redirect()
            ->route('manage.users.index')
            ->with('success', 'Role utilisateur mis a jour.');
    }

    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        $this->requireAdmin();

        if (!$user->locked_at && !$user->password_reset_pending_at) {
            return redirect()
                ->route('manage.users.index')
                ->withErrors(['user' => 'Ce compte n est ni bloque ni en attente de reconnexion.']);
        }

        $data = $request->validate([
            'password' => PasswordRules::defaultsWithConfirmation(),
        ], [
            'password.min' => 'Le mot de passe doit contenir au moins 12 caracteres.',
            'password.regex' => 'Le mot de passe doit contenir une majuscule, une minuscule, un chiffre et un caractere special.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $user->forceFill([
            'password' => Hash::make((string) $data['password']),
            'failed_login_attempts' => 0,
            'last_failed_login_at' => null,
            'locked_at' => null,
            'password_reset_pending_at' => now(),
            'login_token_hash' => null,
            'login_token_expires_at' => null,
            'remember_token' => null,
        ])->save();

        UserLog::logAction(
            (int) $request->user()->id,
            'admin_password_reset',
            'manage.users.password',
            'target_user:' . (string) $user->id
        );

        return redirect()
            ->route('manage.users.index')
            ->with('success', 'Mot de passe reinitialise. Le compte redeviendra actif apres la prochaine connexion reussie.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->requireAdmin();

        $authUser = request()->user();
        if ($authUser && (int) $authUser->id === (int) $user->id) {
            return redirect()
                ->route('manage.users.index')
                ->withErrors(['user' => 'Tu ne peux pas supprimer ton propre compte.']);
        }

        if ($user->worlds()->exists()) {
            return redirect()
                ->route('manage.users.index')
                ->withErrors(['user' => 'Supprime d abord les mondes associes a ce compte.']);
        }

        $user->delete();

        return redirect()
            ->route('manage.users.index')
            ->with('success', 'Compte utilisateur supprime.');
    }
}
