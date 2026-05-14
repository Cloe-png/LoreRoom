<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use App\Support\TrashManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrashController extends Controller
{
    private $trashManager;

    public function __construct(TrashManager $trashManager)
    {
        $this->trashManager = $trashManager;
    }

    public function index(): View
    {
        $currentWorldId = $this->currentWorldId();
        $user = request()->user();

        return view('manage.trash.index', [
            'sections' => $this->trashManager->getSections($user, $currentWorldId),
        ]);
    }

    public function restore(Request $request, string $type, int $id): RedirectResponse
    {
        $user = $request->user();
        $currentWorldId = $this->currentWorldId();
        $record = $this->trashManager->restore($type, $id, $user, $currentWorldId);

        if ($type === 'world' && $user && !(int) ($user->current_world_id ?? 0)) {
            $user->forceFill(['current_world_id' => (int) $record->id])->save();
            $request->session()->put('selected_world_id', (int) $record->id);
        }

        UserLog::logAction((int) $request->user()->id, 'trash_restore', 'manage.trash.restore', $type);

        return back()->with('success', 'Element restaure depuis la corbeille.');
    }

    public function destroy(Request $request, string $type, int $id): RedirectResponse
    {
        $this->trashManager->forceDelete($type, $id, $request->user(), $this->currentWorldId());
        UserLog::logAction((int) $request->user()->id, 'trash_force_delete', 'manage.trash.destroy', $type);

        return back()->with('success', 'Element supprime definitivement.');
    }

    public function empty(Request $request): RedirectResponse
    {
        $deleted = $this->trashManager->emptyTrash($request->user(), $this->currentWorldId());
        UserLog::logAction((int) $request->user()->id, 'trash_emptied', 'manage.trash.empty', (string) $deleted);

        return back()->with('success', sprintf('Corbeille videe. %d element(s) supprime(s) definitivement.', $deleted));
    }
}
