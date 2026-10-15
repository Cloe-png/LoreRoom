<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Character;
use App\Models\Chronicle;
use App\Models\Place;
use App\Models\World;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class ChapterController extends Controller
{
    public function index(Request $request)
    {
        $chapters = Chapter::query()
            ->with([
                'characters' => function ($query) {
                    $query->select([
                        'characters.id',
                        'characters.name',
                        'characters.first_name',
                        'characters.last_name',
                        'characters.preferred_color',
                        'characters.image_path',
                    ]);
                },
                'places:id,name,region',
                'chronicles:id,world_id,title,event_date,event_place_id,summary',
            ])
            ->withCount(['characters', 'places', 'chronicles'])
            ->orderByRaw('chapter_date IS NULL')
            ->orderByDesc('chapter_date')
            ->orderByDesc('id')
            ->paginate(12);

        return view('manage.chapters.index', compact('chapters'));
    }

    public function create(Request $request)
    {
        return view('manage.chapters.create', $this->formPayload($request));
    }

    public function store(Request $request)
    {
        $world = $this->currentWorldOrFail($request);

        $data = $this->validateInput($request, $world->id);
        $chapter = Chapter::create([
            'world_id' => $world->id,
            'chapter_date' => $data['chapter_date'],
            'title' => $data['title'],
            'summary' => $data['summary'] ?? null,
            'description' => $data['description'] ?? null,
        ]);

        $this->syncChapterRelations($chapter, $data['character_ids'], $data['place_ids']);
        $this->refreshAutomaticContent($chapter, $data['character_details']);

        return redirect()
            ->route('manage.chapters.show', $chapter)
            ->with('success', __('app.chapters.create'));
    }

    public function show(Chapter $chapter)
    {
        $chapter->load([
            'characters' => function ($query) {
                $query->select([
                    'characters.id',
                    'characters.name',
                    'characters.first_name',
                    'characters.last_name',
                    'characters.role',
                    'characters.preferred_color',
                    'characters.image_path',
                ]);
            },
            'places:id,name,region',
            'chronicles' => function ($query) {
                $query->with(['eventPlace:id,name,region', 'linkedCharacters:id,name,first_name,last_name'])
                    ->orderByRaw('event_date IS NULL, event_date ASC')
                    ->orderBy('title');
            },
        ]);

        return view('manage.chapters.show', compact('chapter'));
    }

    public function edit(Request $request, Chapter $chapter)
    {
        return view('manage.chapters.edit', array_merge(
            $this->formPayload($request),
            compact('chapter')
        ));
    }

    public function update(Request $request, Chapter $chapter)
    {
        $world = $this->currentWorldOrFail($request);
        $data = $this->validateInput($request, $world->id, $chapter);

        $chapter->update([
            'chapter_date' => $data['chapter_date'],
            'title' => $data['title'],
            'summary' => $data['summary'] ?? null,
            'description' => $data['description'] ?? null,
        ]);

        $this->syncChapterRelations($chapter, $data['character_ids'], $data['place_ids']);
        $this->refreshAutomaticContent($chapter, $data['character_details']);

        return redirect()
            ->route('manage.chapters.show', $chapter)
            ->with('success', __('app.chapters.edit'));
    }

    public function destroy(Chapter $chapter)
    {
        $chapter->delete();

        return redirect()
            ->route('manage.chapters.index')
            ->with('success', __('app.chapters.delete'));
    }

    private function formPayload(Request $request): array
    {
        $world = $this->currentWorldOrFail($request);

        return [
            'world' => $world,
            'characters' => Character::query()
                ->with(['outfitImages:id,character_id,mode,image_path,caption,sort_order'])
                ->select(['id', 'name', 'first_name', 'last_name', 'role', 'appearance_description', 'preferred_color', 'image_path'])
                ->orderBy('name')
                ->get(),
            'places' => Place::query()
                ->select(['id', 'name', 'region'])
                ->orderBy('name')
                ->get(),
        ];
    }

    private function validateInput(Request $request, int $worldId, ?Chapter $chapter = null): array
    {
        $data = $request->validate([
            'chapter_date' => ['required', 'date'],
            'title' => ['required', 'string', 'max:180'],
            'summary' => ['nullable', 'string', 'max:800'],
            'description' => ['nullable', 'string', 'max:12000'],
            'character_ids' => ['nullable', 'array'],
            'character_ids.*' => [
                'required',
                'integer',
                Rule::exists('characters', 'id')->where(fn ($query) => $query->where('world_id', $worldId)),
            ],
            'character_details' => ['nullable', 'array'],
            'character_details.*.outfit_source' => ['nullable', 'string', Rule::in(['photo', 'description'])],
            'character_details.*.outfit' => ['nullable', 'string', 'max:255'],
            'character_details.*.outfit_image_path' => ['nullable', 'string', 'max:255'],
            'character_details.*.emotion' => ['nullable', 'string', 'max:120'],
            'place_ids' => ['nullable', 'array'],
            'place_ids.*' => [
                'required',
                'integer',
                Rule::exists('places', 'id')->where(fn ($query) => $query->where('world_id', $worldId)),
            ],
        ]);

        $characterIds = array_values(array_unique(array_map('intval', $data['character_ids'] ?? [])));
        $placeIds = array_values(array_unique(array_map('intval', $data['place_ids'] ?? [])));
        $characterDetails = (array) ($data['character_details'] ?? []);
        $existingCharacterIds = $chapter ? $chapter->characters()->pluck('characters.id')->map(function ($characterId) {
            return (int) $characterId;
        })->all() : [];

        if ($characterIds === [] && $placeIds === []) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'character_ids' => __('app.chapters.empty_help'),
            ]);
        }

        foreach ($characterIds as $characterId) {
            $hasSubmittedDetails = array_key_exists($characterId, $characterDetails) || array_key_exists((string) $characterId, $characterDetails);
            $details = (array) ($characterDetails[$characterId] ?? $characterDetails[(string) $characterId] ?? []);
            $emotion = trim((string) ($details['emotion'] ?? ''));

            if ($chapter && in_array($characterId, $existingCharacterIds, true) && !$hasSubmittedDetails) {
                continue;
            }

            if ($emotion === '') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "character_details.$characterId" => __('app.chapters.character_details_required'),
                ]);
            }
        }

        return [
            'chapter_date' => $data['chapter_date'],
            'title' => trim((string) $data['title']),
            'summary' => trim((string) ($data['summary'] ?? '')) ?: null,
            'description' => trim((string) ($data['description'] ?? '')) ?: null,
            'character_ids' => $characterIds,
            'character_details' => $this->normalizeCharacterDetails($characterIds, $characterDetails),
            'place_ids' => $placeIds,
        ];
    }

    private function syncChapterRelations(Chapter $chapter, array $characterIds, array $placeIds): void
    {
        $chronicleIds = Chronicle::query()
            ->where('world_id', (int) $chapter->world_id)
            ->where(function ($query) use ($characterIds, $placeIds) {
                if ($characterIds !== []) {
                    $query->whereHas('linkedCharacters', function ($subQuery) use ($characterIds) {
                        $subQuery->whereIn('characters.id', $characterIds);
                    });
                }

                if ($placeIds !== []) {
                    $method = $characterIds !== [] ? 'orWhereIn' : 'whereIn';
                    $query->{$method}('event_place_id', $placeIds);
                }
            })
            ->orderByRaw('event_date IS NULL, event_date ASC')
            ->orderBy('title')
            ->pluck('id')
            ->all();

        $chapter->characters()->sync($characterIds);
        $chapter->places()->sync($placeIds);
        $chapter->chronicles()->sync($chronicleIds);
    }

    private function refreshAutomaticContent(Chapter $chapter, array $characterDetails = []): void
    {
        $chapter->load([
            'characters' => function ($query) {
                $query->select([
                    'characters.id',
                    'characters.name',
                    'characters.first_name',
                    'characters.last_name',
                    'characters.role',
                    'characters.appearance_description',
                    'characters.preferred_color',
                    'characters.image_path',
                ]);
            },
            'characters.outfitImages:id,character_id,mode,image_path,caption,sort_order',
            'places:id,name,region',
            'chronicles:id,title,event_date,event_place_id',
            'chronicles.linkedCharacters:id,name,first_name,last_name',
        ]);

        $characterFeelings = $this->buildCharacterFeelings(
            $chapter->characters,
            $chapter->chronicles,
            $chapter->places,
            $characterDetails
        );

        $chapter->forceFill([
            'character_feelings' => $characterFeelings,
        ])->save();
    }

    private function buildCharacterFeelings(Collection $characters, Collection $chronicles, Collection $places, array $characterDetails = []): array
    {
        $placeNames = $places->pluck('name')->filter()->values()->all();

        return $characters->map(function (Character $character) use ($chronicles, $placeNames, $characterDetails) {
            $role = trim((string) ($character->role ?? ''));
            $details = (array) ($characterDetails[$character->id] ?? $characterDetails[(string) $character->id] ?? []);
            $outfitSource = trim((string) ($details['outfit_source'] ?? ''));
            $outfit = trim((string) ($details['outfit'] ?? ''));
            $outfitImagePath = trim((string) ($details['outfit_image_path'] ?? ''));
            $emotion = trim((string) ($details['emotion'] ?? ''));
            if ($outfit === '' && $outfitSource === 'description') {
                $fallbackDescription = trim((string) ($character->appearance_description ?? ''));
                if ($fallbackDescription !== '') {
                    $outfit = $fallbackDescription;
                }
            } elseif ($outfit === '' && $outfitSource === 'photo' && $outfitImagePath !== '') {
                $photo = $character->outfitImages->firstWhere('image_path', $outfitImagePath);
                if ($photo) {
                    $photoLabel = trim((string) ($photo->caption ?? '')) ?: trim((string) ($photo->mode ?? ''));
                    if ($photoLabel !== '') {
                        $outfit = $photoLabel;
                    }
                }
            }

            $noteParts = [];
            if ($placeNames !== []) {
                $noteParts[] = 'Lieux: ' . implode(', ', $placeNames);
            }
            if ($outfit !== '') {
                $noteParts[] = 'Tenue: ' . $outfit;
            }
            if ($emotion !== '') {
                $noteParts[] = 'Ressenti: ' . $emotion;
            }

            return [
                'character_id' => (int) $character->id,
                'character_name' => $character->display_name,
                'character_role' => $role !== '' ? $role : null,
                'outfit' => $outfit !== '' ? $outfit : null,
                'outfit_source' => $outfitSource !== '' ? $outfitSource : null,
                'outfit_image_path' => $outfitImagePath !== '' ? $outfitImagePath : null,
                'emotion' => $emotion !== '' ? $emotion : null,
                'note' => $noteParts !== [] ? implode(' · ', $noteParts) : 'Aucun détail',
                'chronicles_count' => 0,
            ];
            $directCount = $chronicles->filter(function (Chronicle $chronicle) use ($character) {
                return $chronicle->linkedCharacters->contains('id', $character->id);
            })->count();

            if ($directCount >= 4) {
                $mood = 'Sous tension';
                $note = 'Le personnage porte une charge narrative forte et traverse plusieurs points de bascule.';
            } elseif ($directCount >= 2) {
                $mood = 'Impliqué';
                $note = 'Le personnage intervient à plusieurs étapes du chapitre avec une présence marquée.';
            } elseif ($directCount === 1) {
                $mood = 'À l’écoute';
                $note = 'Le personnage apparaît au bon moment et capte l’essentiel de la scène.';
            } else {
                $mood = 'En retrait';
                $note = 'Le personnage reste en arrière-plan, surtout porté par l’ambiance des lieux.';
            }

            if ($placeNames !== []) {
                $note .= ' Lieux traversés : ' . implode(', ', $placeNames) . '.';
            }

            if ($role !== '') {
                $note = 'En tant que ' . $role . ', ' . lcfirst($note);
            }

            if ($outfit !== '') {
                $note .= ' Tenue : ' . $outfit . '.';
            } elseif ($outfitSource === 'description') {
                $fallbackDescription = trim((string) ($character->appearance_description ?? ''));
                if ($fallbackDescription !== '') {
                    $note .= ' Tenue : ' . $fallbackDescription . '.';
                    $outfit = $fallbackDescription;
                }
            } elseif ($outfitSource === 'photo' && $outfitImagePath !== '') {
                $photo = $character->outfitImages->firstWhere('image_path', $outfitImagePath);
                if ($photo) {
                    $photoLabel = trim((string) ($photo->caption ?? '')) ?: trim((string) ($photo->mode ?? ''));
                    if ($photoLabel !== '') {
                        $note .= ' Tenue : ' . $photoLabel . '.';
                        $outfit = $photoLabel;
                    }
                }
            }

            if ($emotion !== '') {
                $note .= ' Ressenti : ' . $emotion . '.';
            }

            return [
                'character_id' => (int) $character->id,
                'character_name' => $character->display_name,
                'character_role' => $role !== '' ? $role : null,
                'outfit' => $outfit !== '' ? $outfit : null,
                'outfit_source' => $outfitSource !== '' ? $outfitSource : null,
                'outfit_image_path' => $outfitImagePath !== '' ? $outfitImagePath : null,
                'emotion' => $emotion !== '' ? $emotion : null,
                'mood' => $mood,
                'note' => $note,
                'chronicles_count' => $directCount,
            ];
        })->values()->all();
    }

    private function normalizeCharacterDetails(array $characterIds, array $characterDetails): array
    {
        $normalized = [];

        foreach ($characterIds as $characterId) {
            $details = (array) ($characterDetails[$characterId] ?? $characterDetails[(string) $characterId] ?? []);
            $outfitSource = trim((string) ($details['outfit_source'] ?? ''));
            $normalized[(int) $characterId] = [
                'outfit_source' => $outfitSource !== '' ? $outfitSource : null,
                'outfit' => trim((string) ($details['outfit'] ?? '')),
                'outfit_image_path' => trim((string) ($details['outfit_image_path'] ?? '')),
                'emotion' => trim((string) ($details['emotion'] ?? '')),
            ];
        }

        return $normalized;
    }

    private function currentWorldOrFail(Request $request): World
    {
        $worldId = (int) $request->session()->get('selected_world_id', 0);
        if ($worldId <= 0) {
            abort(404);
        }

        return World::query()->whereKey($worldId)->firstOrFail();
    }
}
