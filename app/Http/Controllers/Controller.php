<?php

namespace App\Http\Controllers;

use App\Models\World;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected const IMAGE_FILE_RULES = ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:4096'];
    protected const AUDIO_FILE_RULES = ['nullable', 'file', 'mimes:mp3', 'max:20480'];

    protected function imageFileRules(): array
    {
        return self::IMAGE_FILE_RULES;
    }

    protected function audioFileRules(): array
    {
        return self::AUDIO_FILE_RULES;
    }

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
            throw new NotFoundHttpException(__('app.messages.active_world_missing'));
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
        if (!$user || !$user->isAdmin()) {
            throw new AccessDeniedHttpException(__('app.messages.admin_required'));
        }
    }
}
