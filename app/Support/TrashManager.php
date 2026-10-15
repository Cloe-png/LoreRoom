<?php

namespace App\Support;

use App\Models\Chapter;
use App\Models\Character;
use App\Models\Chronicle;
use App\Models\Faction;
use App\Models\Job;
use App\Models\LoreEntry;
use App\Models\Place;
use App\Models\Species;
use App\Models\World;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class TrashManager
{
    /**
     * @return array<string, array{title:string, type:string, name:string, model:class-string<Model>}>
     */
    private function sections(): array
    {
        return [
            'world' => [
                'title' => 'Mondes',
                'type' => 'world',
                'name' => 'name',
                'model' => World::class,
            ],
            'character' => [
                'title' => 'Personnages',
                'type' => 'character',
                'name' => 'display_name',
                'model' => Character::class,
            ],
            'chapter' => [
                'title' => 'Chapitres',
                'type' => 'chapter',
                'name' => 'title',
                'model' => Chapter::class,
            ],
            'chronicle' => [
                'title' => 'Chroniques',
                'type' => 'chronicle',
                'name' => 'title',
                'model' => Chronicle::class,
            ],
            'place' => [
                'title' => 'Lieux',
                'type' => 'place',
                'name' => 'name',
                'model' => Place::class,
            ],
            'job' => [
                'title' => 'Métiers',
                'type' => 'job',
                'name' => 'name',
                'model' => Job::class,
            ],
            'lore' => [
                'title' => 'Lore',
                'type' => 'lore',
                'name' => 'title',
                'model' => LoreEntry::class,
            ],
            'species' => [
                'title' => 'Espèces',
                'type' => 'species',
                'name' => 'name',
                'model' => Species::class,
            ],
            'faction' => [
                'title' => 'Factions',
                'type' => 'faction',
                'name' => 'name',
                'model' => Faction::class,
            ],
        ];
    }

    /**
     * @return array<int, array{title:string,type:string,name:string,items:Collection<int,Model>}>
     */
    public function getSections(?User $user, ?int $currentWorldId): array
    {
        $sections = [];

        foreach ($this->sections() as $section) {
            $model = $section['model'];
            $query = $model::query()->onlyTrashed();

            if ($model === World::class && $user) {
                $query->where('user_id', (int) $user->id);
            } elseif ($currentWorldId && $this->hasWorldIdColumn($model)) {
                $query->where('world_id', $currentWorldId);
            }

            $items = $query
                ->orderBy('deleted_at', 'desc')
                ->get();

            $sections[] = [
                'title' => $section['title'],
                'type' => $section['type'],
                'name' => $section['name'],
                'items' => $items,
            ];
        }

        return $sections;
    }

    /**
     * @return Model
     */
    public function restore(string $type, int $id, ?User $user, ?int $currentWorldId): Model
    {
        $record = $this->findTrashedRecord($type, $id, $user, $currentWorldId);
        $record->restore();

        return $record->fresh();
    }

    public function forceDelete(string $type, int $id, ?User $user, ?int $currentWorldId): void
    {
        $record = $this->findTrashedRecord($type, $id, $user, $currentWorldId);
        $record->forceDelete();
    }

    public function emptyTrash(?User $user, ?int $currentWorldId): int
    {
        $deleted = 0;

        foreach ($this->sections() as $section) {
            $model = $section['model'];
            $query = $model::query()->onlyTrashed();

            if ($model === World::class && $user) {
                $query->where('user_id', (int) $user->id);
            } elseif ($currentWorldId && $this->hasWorldIdColumn($model)) {
                $query->where('world_id', $currentWorldId);
            }

            $deleted += $query->forceDelete();
        }

        return $deleted;
    }

    public function pruneOlderThan(Carbon $threshold): int
    {
        $deleted = 0;

        foreach ($this->sections() as $section) {
            $model = $section['model'];
            $query = $model::query()->onlyTrashed()->where('deleted_at', '<', $threshold);
            $deleted += $query->forceDelete();
        }

        return $deleted;
    }

    /**
     * @return Model
     */
    private function findTrashedRecord(string $type, int $id, ?User $user, ?int $currentWorldId): Model
    {
        $section = $this->sections()[$this->normalizeType($type)] ?? null;

        if (!$section) {
            throw new InvalidArgumentException('Type de corbeille inconnu.');
        }

        $model = $section['model'];
        $query = $model::query()->onlyTrashed()->whereKey($id);

        if ($model === World::class && $user) {
            $query->where('user_id', (int) $user->id);
        } elseif ($currentWorldId && $this->hasWorldIdColumn($model)) {
            $query->where('world_id', $currentWorldId);
        }

        $record = $query->first();

        if (!$record) {
            throw new InvalidArgumentException('Élément introuvable dans la corbeille.');
        }

        return $record;
    }

    private function normalizeType(string $type): string
    {
        $type = trim(strtolower($type));

        if ($type === 'loreentry') {
            return 'lore';
        }

        return $type;
    }

    private function hasWorldIdColumn(string $model): bool
    {
        return in_array($model, [
            Character::class,
            Chapter::class,
            Chronicle::class,
            Place::class,
            Job::class,
            LoreEntry::class,
            Species::class,
            Faction::class,
        ], true);
    }
}
