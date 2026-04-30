<?php

namespace App\Http\Controllers;

use App\Models\World;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function currentWorldId(): ?int
    {
        $worldId = (int) session('selected_world_id', 0);

        return $worldId > 0 ? $worldId : null;
    }

    protected function currentWorld(): ?World
    {
        $world = request()->attributes->get('active_world');
        if ($world instanceof World) {
            return $world;
        }

        $worldId = $this->currentWorldId();
        if (!$worldId || !Auth::check()) {
            return null;
        }

        return Auth::user()
            ->worlds()
            ->whereKey($worldId)
            ->first();
    }

    protected function requireCurrentWorldId(): int
    {
        $worldId = $this->currentWorldId();
        if (!$worldId) {
            throw new NotFoundHttpException('Monde actif introuvable.');
        }

        return $worldId;
    }

    protected function abortIfOutsideCurrentWorld(int $recordWorldId): void
    {
        $currentWorldId = $this->currentWorldId();
        if ($currentWorldId && $recordWorldId !== $currentWorldId) {
            throw new NotFoundHttpException();
        }
    }

    protected function requireAdmin(): void
    {
        $user = Auth::user();
        if (!$user || (string) ($user->role ?? '') !== 'admin') {
            throw new AccessDeniedHttpException('Accès administrateur requis.');
        }
    }
}
