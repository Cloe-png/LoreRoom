<?php

namespace App\Http\Controllers;

use App\Models\Character;
use Illuminate\Support\Collection;

class GenealogyController extends Controller
{
    public function index()
    {
        $characters = Character::query()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'name', 'first_name', 'last_name', 'gender', 'status', 'father_id', 'mother_id', 'image_path']);

        $selectedId = (int) request('character_id', 0);
        if ($selectedId <= 0 || !$characters->firstWhere('id', $selectedId)) {
            $selectedId = (int) ($characters->first()->id ?? 0);
        }

        [$nodes, $edges] = $this->buildFamilyGraph($characters, $selectedId, 3);
        $layout = $this->buildLayout($nodes);

        return view('manage.genealogy.index', [
            'characters' => $characters,
            'selectedId' => $selectedId,
            'nodes' => $nodes,
            'edges' => $edges,
            'layout' => $layout,
        ]);
    }

    private function buildFamilyGraph(Collection $characters, int $rootId, int $depth): array
    {
        if ($rootId <= 0) {
            return [collect(), collect()];
        }

        $byId = $characters->keyBy('id');
        $levels = collect([$rootId => 0]);
        $queue = collect([[$rootId, 0]]);
        $edges = collect();

        while ($queue->isNotEmpty()) {
            [$currentId, $level] = $queue->shift();
            $current = $byId->get($currentId);
            if (!$current || abs($level) >= $depth) {
                continue;
            }

            foreach (['father_id' => -1, 'mother_id' => -1] as $parentField => $delta) {
                $parentId = (int) ($current->{$parentField} ?? 0);
                if ($parentId > 0 && $byId->has($parentId)) {
                    $edges->push(['from' => $parentId, 'to' => $currentId, 'label' => 'parent']);
                    $next = $level + $delta;
                    if (!$levels->has($parentId) || abs($next) < abs((int) $levels->get($parentId))) {
                        $levels->put($parentId, $next);
                        $queue->push([$parentId, $next]);
                    }
                }
            }

            $children = $characters->filter(function ($character) use ($currentId) {
                return (int) $character->father_id === $currentId || (int) $character->mother_id === $currentId;
            });

            foreach ($children as $child) {
                $edges->push(['from' => $currentId, 'to' => (int) $child->id, 'label' => 'enfant']);
                $next = $level + 1;
                if (!$levels->has((int) $child->id) || abs($next) < abs((int) $levels->get((int) $child->id))) {
                    $levels->put((int) $child->id, $next);
                    $queue->push([(int) $child->id, $next]);
                }
            }
        }

        $nodes = $levels
            ->map(function ($level, $id) use ($byId) {
                $character = $byId->get((int) $id);
                return [
                    'id' => (int) $id,
                    'name' => $character ? $character->display_name : ('#' . $id),
                    'gender' => $character->gender ?? null,
                    'status' => $character->status ?? null,
                    'image_path' => $character->image_path ?? null,
                    'level' => (int) $level,
                ];
            })
            ->values()
            ->sortBy([['level', 'asc'], ['name', 'asc']])
            ->values();

        $edges = $edges
            ->unique(function ($edge) {
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
}

