<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            ->with('success', 'Rôle utilisateur mis à jour.');
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
                ->withErrors(['user' => 'Supprime d’abord les mondes associés à ce compte.']);
        }

        $user->delete();

        return redirect()
            ->route('manage.users.index')
            ->with('success', 'Compte utilisateur supprimé.');
    }
}
