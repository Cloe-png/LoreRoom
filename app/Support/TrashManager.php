<?php

namespace App\Support;

use App\Models\Character;
use App\Models\Chronicle;
use App\Models\Faction;
use App\Models\Job;
use App\Models\LoreEntry;
use App\Models\Place;
use App\Models\Species;
use App\Models\User;
use App\Models\World;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TrashManager
{
    public function getSections(?User $user, ?int $worldId): array
    {
        return [
            [
                'title' => 'Mondes supprimés',
                'type' => 'world',
                'name' => 'name',
                'items' => $user ? $user->worlds()->onlyTrashed()->latest('deleted_at')->get() : collect(),
            ],
            [
                'title' => 'Personnages supprimés',
                'type' => 'character',
                'name' => 'display_name',
                'items' => $worldId ? Character::onlyTrashed()->where('world_id', $worldId)->latest('deleted_at')->get() : collect(),
            ],
            [
                'title' => 'Lieux supprimés',
                'type' => 'place',
                'name' => 'name',
                'items' => $worldId ? Place::onlyTrashed()->where('world_id', $worldId)->latest('deleted_at')->get() : collect(),
            ],
            [
                'title' => 'Chroniques supprimées',
                'type' => 'chronicle',
                'name' => 'title',
                'items' => $worldId ? Chronicle::onlyTrashed()->where('world_id', $worldId)->latest('deleted_at')->get() : collect(),
            ],
            [
                'title' => 'Factions supprimées',
                'type' => 'faction',
                'name' => 'name',
                'items' => $worldId ? Faction::onlyTrashed()->where('world_id', $worldId)->latest('deleted_at')->get() : collect(),
            ],
            [
                'title' => 'Entrées de lore supprimées',
                'type' => 'lore',
                'name' => 'title',
                'items' => $worldId ? LoreEntry::onlyTrashed()->where('world_id', $worldId)->latest('deleted_at')->get() : collect(),
            ],
            [
                'title' => 'Espèces supprimées',
                'type' => 'species',
                'name' => 'name',
                'items' => $worldId ? Species::onlyTrashed()->where('world_id', $worldId)->latest('deleted_at')->get() : collect(),
            ],
            [
                'title' => 'Métiers supprimés',
                'type' => 'job',
                'name' => 'name',
                'items' => $worldId
                    ? Job::onlyTrashed()->where('world_id', $worldId)->where('is_default', false)->latest('deleted_at')->get()
                    : collect(),
            ],
        ];
    }

    public function forceDelete(string $type, int $id, ?User $user, ?int $worldId): void
    {
        $record = $this->resolveRecord($type, $id, $user, $worldId);
        $record->forceDelete();
    }

    public function restore(string $type, int $id, ?User $user, ?int $worldId): Model
    {
        $record = $this->resolveRecord($type, $id, $user, $worldId);
        $record->restore();

        return $record;
    }

    public function emptyTrash(?User $user, ?int $worldId): int
    {
        $deleted = 0;

        foreach ($this->trashQueries($user, $worldId) as $query) {
            /** @var \Illuminate\Database\Eloquent\Model $record */
            foreach ($query->get() as $record) {
                $record->forceDelete();
                $deleted++;
            }
        }

        return $deleted;
    }

    public function pruneOlderThan(CarbonInterface $cutoff): int
    {
        $deleted = 0;

        foreach ($this->trashQueries(null, null, $cutoff, true) as $query) {
            /** @var \Illuminate\Database\Eloquent\Model $record */
            foreach ($query->get() as $record) {
                $record->forceDelete();
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * @return array<int, Builder>
     */
    private function trashQueries(?User $user, ?int $worldId, ?CarbonInterface $cutoff = null, bool $pruneAll = false): array
    {
        $worldsQuery = $user ? $user->worlds()->onlyTrashed() : World::onlyTrashed();

        if ($pruneAll) {
            return [
                $this->applyCutoff(World::onlyTrashed(), $cutoff),
                $this->applyCutoff(Character::onlyTrashed(), $cutoff),
                $this->applyCutoff(Place::onlyTrashed(), $cutoff),
                $this->applyCutoff(Chronicle::onlyTrashed(), $cutoff),
                $this->applyCutoff(Faction::onlyTrashed(), $cutoff),
                $this->applyCutoff(LoreEntry::onlyTrashed(), $cutoff),
                $this->applyCutoff(Species::onlyTrashed(), $cutoff),
                $this->applyCutoff(Job::onlyTrashed()->where('is_default', false), $cutoff),
            ];
        }

        if (!$worldId) {
            return [
                $this->applyCutoff($worldsQuery, $cutoff),
            ];
        }

        return [
            $this->applyCutoff($worldsQuery, $cutoff),
            $this->applyCutoff(Character::onlyTrashed()->where('world_id', $worldId), $cutoff),
            $this->applyCutoff(Place::onlyTrashed()->where('world_id', $worldId), $cutoff),
            $this->applyCutoff(Chronicle::onlyTrashed()->where('world_id', $worldId), $cutoff),
            $this->applyCutoff(Faction::onlyTrashed()->where('world_id', $worldId), $cutoff),
            $this->applyCutoff(LoreEntry::onlyTrashed()->where('world_id', $worldId), $cutoff),
            $this->applyCutoff(Species::onlyTrashed()->where('world_id', $worldId), $cutoff),
            $this->applyCutoff(Job::onlyTrashed()->where('world_id', $worldId)->where('is_default', false), $cutoff),
        ];
    }

    private function applyCutoff(Builder $query, ?CarbonInterface $cutoff): Builder
    {
        if ($cutoff) {
            $query->where('deleted_at', '<=', $cutoff);
        }

        return $query->latest('deleted_at');
    }

    private function resolveRecord(string $type, int $id, ?User $user, ?int $worldId): Model
    {
        switch ($type) {
            case 'world':
                return $user
                    ? $user->worlds()->onlyTrashed()->whereKey($id)->firstOrFail()
                    : World::onlyTrashed()->whereKey($id)->firstOrFail();

            case 'character':
                return $this->resolveWorldScoped(Character::class, $id, $worldId);

            case 'place':
                return $this->resolveWorldScoped(Place::class, $id, $worldId);

            case 'chronicle':
                return $this->resolveWorldScoped(Chronicle::class, $id, $worldId);

            case 'faction':
                return $this->resolveWorldScoped(Faction::class, $id, $worldId);

            case 'lore':
                return $this->resolveWorldScoped(LoreEntry::class, $id, $worldId);

            case 'species':
                return $this->resolveWorldScoped(Species::class, $id, $worldId);

            case 'job':
                if (!$worldId) {
                    abort(404);
                }

                return Job::onlyTrashed()
                    ->where('world_id', $worldId)
                    ->where('is_default', false)
                    ->whereKey($id)
                    ->firstOrFail();
        }

        abort(404);
    }

    private function resolveWorldScoped(string $modelClass, int $id, ?int $worldId): Model
    {
        if (!$worldId) {
            abort(404);
        }

        return $modelClass::onlyTrashed()
            ->where('world_id', $worldId)
            ->whereKey($id)
            ->firstOrFail();
    }
}
