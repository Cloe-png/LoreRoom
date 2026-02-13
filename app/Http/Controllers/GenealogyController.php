<?php

namespace App\Http\Controllers;

use App\Models\Character;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class GenealogyController extends Controller
{
    public function index()
    {
        $characters = Character::query()
            ->orderByRaw('birth_date IS NULL')
            ->orderBy('birth_date')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get([
                'id',
                'name',
                'first_name',
                'last_name',
                'family_name',
                'gender',
                'status',
                'father_id',
                'mother_id',
                'birth_date',
                'death_date',
                'image_path',
            ]);

        $pivotMode = (string) request('pivot_mode', 'character');
        if (!in_array($pivotMode, ['character', 'family', 'both'], true)) {
            $pivotMode = 'character';
        }

        $families = $characters
            ->map(function ($character) {
                return $this->resolveFamilyName($character);
            })
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $selectedFamily = trim((string) request('family', ''));
        if (($pivotMode === 'family' || $pivotMode === 'both') && ($selectedFamily === '' || !$families->contains($selectedFamily))) {
            $selectedFamily = (string) ($families->first() ?? '');
        }

        $selectedId = (int) request('character_id', 0);
        if ($selectedId <= 0 || !$characters->firstWhere('id', $selectedId)) {
            $selectedId = (int) ($characters->first()->id ?? 0);
        }

        $rootIds = collect();
        if (in_array($pivotMode, ['character', 'both'], true) && $selectedId > 0) {
            $rootIds->push($selectedId);
        }
        if (in_array($pivotMode, ['family', 'both'], true) && $selectedFamily !== '') {
            $familyIds = $characters
                ->filter(function ($character) use ($selectedFamily) {
                    return $this->resolveFamilyName($character) === $selectedFamily;
                })
                ->pluck('id');
            $rootIds = $rootIds->merge($familyIds);
        }
        $rootIds = $rootIds->filter()->unique()->values();

        if ($rootIds->isEmpty() && $selectedId > 0) {
            $rootIds->push($selectedId);
        }

        [$nodes, $edges] = $this->buildFamilyGraph($characters, $rootIds->all(), 3);
        $layout = $this->buildLayout($nodes);

        return view('manage.genealogy.index', [
            'characters' => $characters,
            'selectedId' => $selectedId,
            'families' => $families,
            'selectedFamily' => $selectedFamily,
            'pivotMode' => $pivotMode,
            'nodes' => $nodes,
            'edges' => $edges,
            'layout' => $layout,
        ]);
    }

    private function buildFamilyGraph(Collection $characters, array $rootIds, int $depth): array
    {
        $rootIds = collect($rootIds)->map(function ($id) {
            return (int) $id;
        })->filter()->unique()->values();
        if ($rootIds->isEmpty()) {
            return [collect(), collect()];
        }

        $byId = $characters->keyBy('id');
        $levels = collect();
        $queue = collect();
        foreach ($rootIds as $rootId) {
            if ($byId->has($rootId)) {
                $levels->put($rootId, 0);
                $queue->push([$rootId, 0]);
            }
        }

        if ($queue->isEmpty()) {
            return [collect(), collect()];
        }

        $edges = collect();

        while ($queue->isNotEmpty()) {
            [$currentId, $level] = $queue->shift();
            $current = $byId->get($currentId);
            if (!$current) {
                continue;
            }

            if (abs($level) < $depth) {
                foreach (['father_id' => -1, 'mother_id' => -1] as $parentField => $delta) {
                    $parentId = (int) ($current->{$parentField} ?? 0);
                    if ($parentId > 0 && $byId->has($parentId)) {
                $edges->push([
                    'from' => $parentId,
                    'to' => $currentId,
                    'label' => '',
                    'kind' => 'lineage',
                ]);
                        $next = $level + $delta;
                        if (!$levels->has($parentId) || abs($next) < abs((int) $levels->get($parentId))) {
                            $levels->put($parentId, $next);
                            $queue->push([$parentId, $next]);
                        }
                    }
                }

                $children = $characters
                    ->filter(function ($character) use ($currentId) {
                        return (int) $character->father_id === $currentId || (int) $character->mother_id === $currentId;
                    })
                    ->sort(function ($a, $b) {
                        $dateA = optional($a->birth_date)->format('Y-m-d') ?: '9999-12-31';
                        $dateB = optional($b->birth_date)->format('Y-m-d') ?: '9999-12-31';
                        if ($dateA === $dateB) {
                            return strcmp((string) $a->display_name, (string) $b->display_name);
                        }

                        return strcmp($dateA, $dateB);
                    })
                    ->values();

                foreach ($children as $child) {
                    $edges->push([
                        'from' => $currentId,
                        'to' => (int) $child->id,
                        'label' => '',
                        'kind' => 'lineage',
                    ]);
                    $next = $level + 1;
                    if (!$levels->has((int) $child->id) || abs($next) < abs((int) $levels->get((int) $child->id))) {
                        $levels->put((int) $child->id, $next);
                        $queue->push([(int) $child->id, $next]);
                    }
                }
            }

        }

        // Build sibling links as a chain per sibling group to keep graph readable.
        $includedIds = $levels->keys()->map(fn ($id) => (int) $id)->all();
        $included = $characters->whereIn('id', $includedIds);
        $siblingGroups = [];
        foreach ($included as $character) {
            $fatherId = (int) ($character->father_id ?? 0);
            $motherId = (int) ($character->mother_id ?? 0);
            if ($fatherId <= 0 && $motherId <= 0) {
                continue;
            }
            $key = $fatherId . '-' . $motherId;
            $siblingGroups[$key][] = $character;
        }

        foreach ($siblingGroups as $group) {
            if (count($group) < 2) {
                continue;
            }

            usort($group, function ($a, $b) {
                $dateA = optional($a->birth_date)->format('Y-m-d') ?: '9999-12-31';
                $dateB = optional($b->birth_date)->format('Y-m-d') ?: '9999-12-31';
                if ($dateA === $dateB) {
                    return strcmp((string) $a->display_name, (string) $b->display_name);
                }

                return strcmp($dateA, $dateB);
            });

            for ($i = 0; $i < count($group) - 1; $i++) {
                $left = $group[$i];
                $right = $group[$i + 1];
                $edges->push([
                    'from' => (int) $left->id,
                    'to' => (int) $right->id,
                    'label' => '',
                    'sibling_kind' => $this->resolveSiblingKind($left, $right),
                    'kind' => 'sibling',
                ]);
            }
        }

        $nodes = $levels
            ->map(function ($level, $id) use ($byId) {
                $character = $byId->get((int) $id);
                $birthDate = optional($character->birth_date)->format('Y-m-d');
                $deathDate = optional($character->death_date)->format('Y-m-d');

                return [
                    'id' => (int) $id,
                    'name' => $character ? $character->display_name : ('#' . $id),
                    'gender' => $character->gender ?? null,
                    'status' => $character->status ?? null,
                    'father_id' => $character ? (int) ($character->father_id ?? 0) : 0,
                    'mother_id' => $character ? (int) ($character->mother_id ?? 0) : 0,
                    'image_path' => $this->resolveUsableImagePath($character),
                    'level' => (int) $level,
                    'generation' => $this->generationLabel((int) $level),
                    'birth_date' => $birthDate ?: null,
                    'death_date' => $deathDate ?: null,
                ];
            })
            ->values()
            ->sort(function (array $a, array $b) {
                $levelCmp = ((int) $a['level']) <=> ((int) $b['level']);
                if ($levelCmp !== 0) {
                    return $levelCmp;
                }

                $dateA = (string) ($a['birth_date'] ?? '9999-12-31');
                $dateB = (string) ($b['birth_date'] ?? '9999-12-31');
                $dateCmp = strcmp($dateA, $dateB);
                if ($dateCmp !== 0) {
                    return $dateCmp;
                }

                return strcmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
            })
            ->values();

        $edges = $edges
            ->unique(function ($edge) {
                if (($edge['kind'] ?? '') === 'sibling') {
                    $a = min((int) $edge['from'], (int) $edge['to']);
                    $b = max((int) $edge['from'], (int) $edge['to']);

                    return 'sibling-' . $a . '-' . $b . '-' . ($edge['label'] ?? '');
                }

                return $edge['from'] . '-' . $edge['to'];
            })
            ->values();

        return [$nodes, $edges];
    }

    private function buildLayout(Collection $nodes): array
    {
        if ($nodes->isEmpty()) {
            return ['positions' => [], 'width' => 900, 'height' => 500];
        }

        $groups = $nodes->groupBy('level')->sortKeys();
        $levelGapY = 170;
        $nodeGapX = 220;
        $maxPerRow = max(1, (int) $groups->map->count()->max());

        $positions = [];
        $minLevel = (int) $groups->keys()->min();
        foreach ($groups as $level => $row) {
            $count = $row->count();
            $rowWidth = max(1, $count - 1) * $nodeGapX;
            $startX = 110 + ((($maxPerRow - 1) * $nodeGapX - $rowWidth) / 2);
            $y = 90 + ((int) $level - $minLevel) * $levelGapY;

            foreach ($row->values() as $idx => $node) {
                $positions[(int) $node['id']] = [
                    'x' => (int) round($startX + $idx * $nodeGapX),
                    'y' => (int) round($y),
                ];
            }
        }

        $width = max(900, 220 + ($maxPerRow * $nodeGapX));
        $height = max(500, 180 + ($groups->count() * $levelGapY));

        return [
            'positions' => $positions,
            'width' => $width,
            'height' => $height,
        ];
    }

    private function resolveFamilyName(Character $character): string
    {
        $familyName = trim((string) ($character->family_name ?? ''));
        if ($familyName !== '') {
            return $familyName;
        }

        $lastName = trim((string) ($character->last_name ?? ''));
        if ($lastName !== '') {
            return $lastName;
        }

        $name = trim((string) ($character->name ?? ''));
        if ($name === '') {
            return '';
        }

        $parts = preg_split('/\s+/', $name);

        return trim((string) end($parts));
    }

    private function resolveSiblingKind(Character $character, Character $sibling): string
    {
        $sharedFather = (int) $character->father_id > 0 && (int) $character->father_id === (int) $sibling->father_id;
        $sharedMother = (int) $character->mother_id > 0 && (int) $character->mother_id === (int) $sibling->mother_id;
        $isTwin = !empty($character->birth_date) && !empty($sibling->birth_date)
            && optional($character->birth_date)->format('Y-m-d') === optional($sibling->birth_date)->format('Y-m-d');

        if ($sharedFather && $sharedMother) {
            return $isTwin ? 'twin' : 'full';
        }

        if ($sharedFather || $sharedMother) {
            return 'half';
        }

        return 'sibling';
    }

    private function generationLabel(int $level): string
    {
        if ($level === 0) {
            return 'Generation pivot';
        }

        if ($level < 0) {
            return 'Generation -' . abs($level);
        }

        return 'Generation +' . $level;
    }

    private function resolveUsableImagePath(?Character $character): ?string
    {
        if (!$character || empty($character->image_path)) {
            return null;
        }

        $path = (string) $character->image_path;

        return Storage::disk('public')->exists($path) ? $path : null;
    }
}
