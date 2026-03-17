<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\CharacterEducation;
use App\Models\CharacterGalleryImage;
use App\Models\CharacterItem;
use App\Models\CharacterJob;
use App\Models\CharacterRelation;
use App\Models\Diploma;
use App\Models\Faction;
use App\Models\Job;
use App\Models\Place;
use App\Models\Species;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CharacterController extends Controller
{
    private const AUTO_FAMILY_TAG = '[AUTO_FAMILY]';
    private const AUTO_SIBLING_TAG = '[AUTO_SIBLING]';
    private const CHARACTER_ROLES = [
        'Personnage principal',
        'Personnage secondaire',
        'Antagoniste',
        'Allié',
        'Mentor',
        'Figure historique',
    ];

    public function index()
    {
        $q = trim(request('q', ''));

        $characters = Character::with(['world', 'father', 'mother'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $like = '%' . $q . '%';
                    $sub->where('name', 'like', $like)
                        ->orWhere('first_name', 'like', $like)
                        ->orWhere('last_name', 'like', $like)
                        ->orWhere('family_name', 'like', $like)
                        ->orWhere('aliases', 'like', $like)
                        ->orWhere('status', 'like', $like)
                        ->orWhere('role', 'like', $like)
                        ->orWhere('summary', 'like', $like)
                        ->orWhere('qualities', 'like', $like)
                        ->orWhere('flaws', 'like', $like)
                        ->orWhere('hair_eyes', 'like', $like)
                        ->orWhere('hair_color', 'like', $like)
                        ->orWhere('eye_color', 'like', $like)
                        ->orWhere('clothing_style', 'like', $like)
                        ->orWhereHas('world', function ($worldQuery) use ($like) {
                            $worldQuery->where('name', 'like', $like);
                        })
                        ->orWhereHas('birthPlace', function ($placeQuery) use ($like) {
                            $placeQuery->where('name', 'like', $like);
                        })
                        ->orWhereHas('residencePlace', function ($placeQuery) use ($like) {
                            $placeQuery->where('name', 'like', $like);
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->appends(['q' => $q]);

        return view('manage.characters.index', compact('characters', 'q'));
    }

    public function create()
    {
        $defaultWorld = $this->currentWorld();
        $defaultWorldId = $this->currentWorldId();
        $places = Place::orderBy('name')->get();
        $parents = Character::orderBy('name')->get();
        $spouses = Character::orderBy('name')->get();
        $characters = Character::orderBy('name')->get();
        $factions = Faction::orderBy('name')->get(['id', 'name']);
        $diplomas = Diploma::with('faction')->orderBy('name')->get(['id', 'name', 'faction_id', 'level']);
        $speciesOptions = Species::orderBy('name')->get(['id', 'name']);
        $selectedExIds = old('ex_character_ids', []);
        $relationRows = old('relations', [
            ['to_character_id' => '', 'relation_type' => '', 'is_bidirectional' => '1', 'description' => ''],
        ]);
        $itemRows = old('items', [
            ['name' => '', 'rarity' => '', 'notes' => ''],
        ]);
        $jobRows = old('jobs', [
            ['job_id' => '', 'job_name' => '', 'start_year' => '', 'end_year' => '', 'notes' => ''],
        ]);
        $jobOptions = Job::query()
            ->when($defaultWorldId, function ($query) use ($defaultWorldId) {
                $query->whereNull('world_id')->orWhere('world_id', $defaultWorldId);
            }, function ($query) {
                $query->whereNull('world_id');
            })
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get(['id', 'name', 'is_default']);
        $educationRows = old('educations', [
            ['faction_id' => '', 'diploma_id' => '', 'field' => '', 'start_year' => '', 'end_year' => '', 'notes' => ''],
        ]);
        $selectedSpeciesIds = old('species_ids', []);
        $childrenLinkType = old('children_link_type', 'father');
        $selectedChildrenIds = old('children_ids', []);
        $selectedFullSiblingIds = old('sibling_ids_full', []);
        $selectedTwinSiblingIds = old('sibling_ids_twin', []);
        $selectedHalfSiblingIds = old('sibling_ids_half', []);
        $roleOptions = self::CHARACTER_ROLES;

        return view('manage.characters.create', compact(
            'defaultWorld',
            'places',
            'parents',
            'spouses',
            'selectedExIds',
            'characters',
            'factions',
            'diplomas',
            'speciesOptions',
            'relationRows',
            'itemRows',
            'jobRows',
            'jobOptions',
            'educationRows',
            'selectedSpeciesIds',
            'childrenLinkType',
            'selectedChildrenIds',
            'selectedFullSiblingIds',
            'selectedTwinSiblingIds',
            'selectedHalfSiblingIds',
            'roleOptions'
        ));
    }

    public function store(Request $request)
    {
        $defaultWorldId = $this->requireCurrentWorldId();
        if (!$defaultWorldId) {
            return back()->withErrors(['world' => "Créez d'abord un monde."])->withInput();
        }

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['nullable', 'string', 'max:120'],
            'family_name' => ['nullable', 'string', 'max:120'],
            'aliases' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', Rule::in(['homme', 'femme', 'autre'])],
            'birth_date' => ['nullable', 'date'],
            'death_date' => ['nullable', 'date', 'after_or_equal:birth_date'],
            'father_id' => ['nullable', 'exists:characters,id'],
            'mother_id' => ['nullable', 'exists:characters,id', 'different:father_id'],
            'spouse_id' => ['nullable', 'exists:characters,id', 'different:father_id', 'different:mother_id'],
            'ex_character_ids' => ['nullable', 'array'],
            'ex_character_ids.*' => ['nullable', 'exists:characters,id'],
            'birth_place_id' => ['nullable', 'exists:places,id'],
            'residence_place_id' => ['nullable', 'exists:places,id'],
            'role' => ['required', Rule::in(self::CHARACTER_ROLES)],
            'short_term_goal' => ['nullable', 'string', 'max:3000'],
            'long_term_goal' => ['nullable', 'string', 'max:3000'],
            'secrets' => ['nullable', 'string', 'max:5000'],
            'secrets_is_private' => ['nullable', 'boolean'],
            'has_power' => ['nullable', 'boolean'],
            'power_level' => ['nullable', 'integer', 'min:1', 'max:10'],
            'power_description' => ['nullable', 'string', 'max:3000'],
            'height' => ['nullable', 'string', 'max:120'],
            'hair_eyes' => ['nullable', 'string', 'max:255'],
            'hair_color' => ['nullable', 'string', 'max:120'],
            'eye_color' => ['nullable', 'string', 'max:120'],
            'marks' => ['nullable', 'string', 'max:2000'],
            'clothing_style' => ['nullable', 'string', 'max:2000'],
            'qualities' => ['nullable', 'string', 'max:2000'],
            'flaws' => ['nullable', 'string', 'max:2000'],
            'voice_tics' => ['nullable', 'string', 'max:3000'],
            'voice_audio' => ['nullable', 'file', 'mimetypes:audio/mpeg,audio/wav,audio/ogg,audio/mp4,audio/webm', 'max:20480'],
            'voice_youtube_url' => ['nullable', 'url', 'max:1000', 'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\//i'],
            'summary' => ['nullable', 'string', 'max:3000'],
            'preferred_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'image' => ['nullable', 'image', 'max:4096'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['nullable', 'image', 'max:4096'],
            'gallery_captions' => ['nullable', 'array'],
            'gallery_captions.*' => ['nullable', 'string', 'max:255'],
            'relations' => ['nullable', 'array'],
            'relations.*.to_character_id' => ['nullable', 'exists:characters,id'],
            'relations.*.relation_type' => ['nullable', 'string', 'max:120'],
            'relations.*.description' => ['nullable', 'string', 'max:3000'],
            'relations.*.is_bidirectional' => ['nullable', 'boolean'],
            'items' => ['nullable', 'array'],
            'items.*.name' => ['nullable', 'string', 'max:160'],
            'items.*.rarity' => ['nullable', Rule::in(['commun', 'rare', 'epique', 'legendaire', 'mythique'])],
            'items.*.notes' => ['nullable', 'string', 'max:2000'],
            'jobs' => ['nullable', 'array'],
            'jobs.*.job_id' => ['nullable', Rule::exists('jobs', 'id')],
            'jobs.*.job_name' => ['nullable', 'string', 'max:180'],
            'jobs.*.start_year' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'jobs.*.end_year' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'jobs.*.notes' => ['nullable', 'string', 'max:2000'],
            'educations' => ['nullable', 'array'],
            'educations.*.faction_id' => ['nullable', Rule::exists('factions', 'id')->where('world_id', $defaultWorldId)],
            'educations.*.diploma_id' => ['nullable', 'exists:diplomas,id'],
            'educations.*.field' => ['nullable', 'string', 'max:180'],
            'educations.*.start_year' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'educations.*.end_year' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'educations.*.notes' => ['nullable', 'string', 'max:2000'],
            'species_ids' => ['nullable', 'array'],
            'species_ids.*' => ['nullable', Rule::exists('species', 'id')->where('world_id', $defaultWorldId)],
            'children_link_type' => ['nullable', Rule::in(['father', 'mother'])],
            'children_ids' => ['nullable', 'array'],
            'children_ids.*' => ['nullable', 'exists:characters,id'],
            'sibling_ids_full' => ['nullable', 'array'],
            'sibling_ids_full.*' => ['nullable', 'exists:characters,id'],
            'sibling_ids_twin' => ['nullable', 'array'],
            'sibling_ids_twin.*' => ['nullable', 'exists:characters,id'],
            'sibling_ids_half' => ['nullable', 'array'],
            'sibling_ids_half.*' => ['nullable', 'exists:characters,id'],
        ]);

        $data['has_children'] = $request->boolean('has_children');
        $data['has_brother_sister'] = $request->boolean('has_brother_sister');
        $data['has_power'] = $request->boolean('has_power');
        $data['secrets_is_private'] = $request->boolean('secrets_is_private');
        $data['name'] = trim($data['first_name'] . ' ' . ($data['last_name'] ?? ''));
        $data['family_name'] = trim((string) ($data['family_name'] ?? '')) !== ''
            ? trim((string) $data['family_name'])
            : (trim((string) ($data['last_name'] ?? '')) ?: null);
        $data['world_id'] = $defaultWorldId;
        $data['preferred_color'] = $this->normalizeHexColor($data['preferred_color'] ?? null);
        $spouseId = isset($data['spouse_id']) && $data['spouse_id'] !== '' ? (int) $data['spouse_id'] : null;
        $selectedExIds = collect($data['ex_character_ids'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values()->all();
        unset($data['spouse_id']);
        unset($data['ex_character_ids']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('characters', 'public');
        }
        $voiceYoutubeUrl = $this->normalizeYoutubeUrl($data['voice_youtube_url'] ?? null);
        $hasVoiceAudio = $request->hasFile('voice_audio');
        if ($hasVoiceAudio && $voiceYoutubeUrl) {
            return back()->withErrors([
                'voice_audio' => 'Choisis soit un fichier audio, soit un lien YouTube.',
                'voice_youtube_url' => 'Choisis soit un fichier audio, soit un lien YouTube.',
            ])->withInput();
        }
        if ($request->hasFile('voice_audio')) {
            $data['voice_audio_path'] = $request->file('voice_audio')->store('characters/voices', 'public');
            $data['voice_youtube_url'] = null;
        } else {
            $data['voice_youtube_url'] = $voiceYoutubeUrl;
        }

        $relationRows = $data['relations'] ?? [];
        $itemRows = $data['items'] ?? [];
        $jobRows = $data['jobs'] ?? [];
        $educationRows = $data['educations'] ?? [];
        $speciesIds = collect($data['species_ids'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values()->all();
        $childrenLinkType = $data['children_link_type'] ?? 'father';
        $selectedChildrenIds = collect($data['children_ids'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values()->all();
        $siblingIdsByKind = [
            'full' => collect($data['sibling_ids_full'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values()->all(),
            'twin' => collect($data['sibling_ids_twin'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values()->all(),
            'half' => collect($data['sibling_ids_half'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values()->all(),
        ];
        unset($data['relations']);
        unset($data['items'], $data['jobs']);
        unset($data['educations']);
        unset($data['species_ids']);
        unset($data['children_link_type'], $data['children_ids'], $data['sibling_ids_full'], $data['sibling_ids_twin'], $data['sibling_ids_half']);
        unset($data['gallery_images'], $data['gallery_captions']);

        DB::transaction(function () use ($request, $data, $spouseId, $selectedExIds, $relationRows, $itemRows, $jobRows, $educationRows, $speciesIds, $childrenLinkType, $selectedChildrenIds, $siblingIdsByKind) {
            $character = Character::create($data);
            $impactedSpouseIds = $this->syncSpouseLink($character, $spouseId);
            $this->syncExPartners($character, $selectedExIds);
            $this->syncOutgoingRelations($character, $relationRows);
            $this->syncCharacterItems($character, $itemRows);
            $this->syncCharacterJobs($character, $jobRows);
            $this->syncCharacterEducations($character, $educationRows);
            $this->syncCharacterSpecies($character, $speciesIds);
            $this->addCharacterGalleryImages($character, $request->file('gallery_images', []), $request->input('gallery_captions', []));
            $this->syncChildrenLinks($character, (bool) $data['has_children'], $childrenLinkType, $selectedChildrenIds);
            $this->syncSiblingRelations($character, (bool) $data['has_brother_sister'], $siblingIdsByKind);
            $this->syncAutoFamilyRelationsForCharacters(array_filter([
                $character->id,
                $character->father_id,
                $character->mother_id,
                ...$impactedSpouseIds,
            ]));
        });

        return redirect()->route('manage.characters.index')->with('success', 'Personnage créé avec ses relations.');
    }

    public function show(Character $character)
    {
        $character->load([
            'world',
            'father',
            'mother',
            'spouse',
            'exes',
            'birthPlace',
            'residencePlace',
            'childrenFromFather',
            'childrenFromMother',
            'items',
            'jobs.job',
            'educations.faction',
            'educations.diploma',
            'species',
            'galleryImages',
        ]);

        return view('manage.characters.show', compact('character'));
    }

    public function exportPdf(Character $character)
    {
        $character->load([
            'world',
            'father',
            'mother',
            'spouse',
            'exes',
            'birthPlace',
            'residencePlace',
            'childrenFromFather',
            'childrenFromMother',
            'items',
            'jobs.job',
            'educations.faction',
            'educations.diploma',
            'species',
            'galleryImages',
        ]);

        $portraitDataUri = $this->buildDataUriFromPublicDisk($character->image_path);
        $galleryDataUris = [];
        foreach ($character->galleryImages as $img) {
            $galleryDataUris[$img->id] = $this->buildDataUriFromPublicDisk($img->image_path);
        }

        $pdf = Pdf::loadView('manage.characters.export-pdf', [
            'character' => $character,
            'portraitDataUri' => $portraitDataUri,
            'galleryDataUris' => $galleryDataUris,
        ]);

        return $pdf->download('personnage-' . $character->id . '.pdf');
    }

    public function edit(Character $character)
    {
        $defaultWorld = $this->currentWorld();
        $defaultWorldId = $this->currentWorldId();
        $places = Place::orderBy('name')->get();
        $parents = Character::where('id', '!=', $character->id)->orderBy('name')->get();
        $spouses = Character::where('id', '!=', $character->id)->orderBy('name')->get(); 
        $characters = Character::where('id', '!=', $character->id)->orderBy('name')->get();
        $factions = Faction::orderBy('name')->get(['id', 'name']);
        $diplomas = Diploma::with('faction')->orderBy('name')->get(['id', 'name', 'faction_id', 'level']);
        $speciesOptions = Species::orderBy('name')->get(['id', 'name']);
        $selectedExIds = old('ex_character_ids', $character->exes()->pluck('characters.id')->all());
        $selectedSpeciesIds = old('species_ids', $character->species()->pluck('species.id')->all());
        $fatherChildrenIds = Character::where('father_id', $character->id)->pluck('id')->all();
        $motherChildrenIds = Character::where('mother_id', $character->id)->pluck('id')->all();

        if (old('relations') !== null) {
            $relationRows = old('relations');
        } else {
            $relationRows = $character->outgoingRelations()
                ->where(function ($query) {
                    $query->whereNull('description')
                        ->orWhere('description', 'not like', self::AUTO_FAMILY_TAG . '%')
                        ->where('description', 'not like', self::AUTO_SIBLING_TAG . '%');
                })
                ->orderBy('id')
                ->get()
                ->map(function ($relation) {
                    return [
                        'to_character_id' => (string) $relation->to_character_id,
                        'relation_type' => $relation->relation_type,
                        'description' => $relation->description,
                        'is_bidirectional' => $relation->is_bidirectional ? '1' : '0',
                    ];
                })
                ->all();

            if (empty($relationRows)) {
                $relationRows[] = ['to_character_id' => '', 'relation_type' => '', 'is_bidirectional' => '1', 'description' => ''];
            }
        }

        if (old('items') !== null) {
            $itemRows = old('items');
        } else {
            $itemRows = $character->items()
                ->get()
                ->map(fn ($item) => ['name' => $item->name, 'rarity' => $item->rarity, 'notes' => $item->notes])
                ->all();
            if (empty($itemRows)) {
                $itemRows[] = ['name' => '', 'rarity' => '', 'notes' => ''];
            }
        }

        if (old('jobs') !== null) {
            $jobRows = old('jobs');
        } else {
            $jobRows = $character->jobs()
                ->get()
                ->map(function ($job) {
                    return [
                        'job_id' => $job->job_id,
                        'job_name' => $job->job_name,
                        'start_year' => $job->start_year,
                        'end_year' => $job->end_year,
                        'notes' => $job->notes,
                    ];
                })
                ->all();
            if (empty($jobRows)) {
                $jobRows[] = ['job_id' => '', 'job_name' => '', 'start_year' => '', 'end_year' => '', 'notes' => ''];
            }
        }

        if (old('educations') !== null) {
            $educationRows = old('educations');
        } else {
            $educationRows = $character->educations()
                ->with(['faction', 'diploma'])
                ->get()
                ->map(function ($edu) {
                    return [
                        'faction_id' => $edu->faction_id,
                        'diploma_id' => $edu->diploma_id,
                        'field' => $edu->field,
                        'start_year' => $edu->start_year,
                        'end_year' => $edu->end_year,
                        'notes' => $edu->notes,
                    ];
                })
                ->all();
            if (empty($educationRows)) {
                $educationRows[] = ['faction_id' => '', 'diploma_id' => '', 'field' => '', 'start_year' => '', 'end_year' => '', 'notes' => ''];
            }
        }

        if (old('children_ids') !== null) {
            $selectedChildrenIds = old('children_ids', []);
            $childrenLinkType = old('children_link_type', 'father');
        } else {
            $selectedChildrenIds = !empty($fatherChildrenIds) ? $fatherChildrenIds : $motherChildrenIds;
            $childrenLinkType = !empty($fatherChildrenIds) ? 'father' : 'mother';
        }

        if (old('sibling_ids_full') !== null || old('sibling_ids_twin') !== null || old('sibling_ids_half') !== null) {
            $selectedFullSiblingIds = old('sibling_ids_full', []);
            $selectedTwinSiblingIds = old('sibling_ids_twin', []);
            $selectedHalfSiblingIds = old('sibling_ids_half', []);
        } else {
            $siblingRows = $character->outgoingRelations()
                ->where('description', 'like', self::AUTO_SIBLING_TAG . '%')
                ->get(['to_character_id', 'relation_type', 'sibling_kind']);
            $selectedFullSiblingIds = [];
            $selectedTwinSiblingIds = [];
            $selectedHalfSiblingIds = [];
            foreach ($siblingRows as $row) {
                $toId = (int) $row->to_character_id;
                if ($toId <= 0) {
                    continue;
                }
                $kind = (string) $row->sibling_kind !== ''
                    ? (string) $row->sibling_kind
                    : $this->siblingKindFromRelationType((string) $row->relation_type);
                if ($kind === 'twin') {
                    $selectedTwinSiblingIds[] = $toId;
                } elseif ($kind === 'half') {
                    $selectedHalfSiblingIds[] = $toId;
                } else {
                    $selectedFullSiblingIds[] = $toId;
                }
            }
            $selectedFullSiblingIds = array_values(array_unique($selectedFullSiblingIds));
            $selectedTwinSiblingIds = array_values(array_unique($selectedTwinSiblingIds));
            $selectedHalfSiblingIds = array_values(array_unique($selectedHalfSiblingIds));
        }

        $existingGallery = $character->galleryImages()->get();
        $roleOptions = self::CHARACTER_ROLES;
        $jobOptions = Job::query()
            ->when($defaultWorldId, function ($query) use ($defaultWorldId) {
                $query->whereNull('world_id')->orWhere('world_id', $defaultWorldId);
            }, function ($query) {
                $query->whereNull('world_id');
            })
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get(['id', 'name', 'is_default']);

        return view('manage.characters.edit', compact(
            'character',
            'defaultWorld',
            'places',
            'parents',
            'spouses',
            'selectedExIds',
            'characters',
            'factions',
            'diplomas',
            'relationRows',
            'itemRows',
            'jobRows',
            'jobOptions',
            'educationRows',
            'speciesOptions',
            'selectedSpeciesIds',
            'existingGallery',
            'childrenLinkType',
            'selectedChildrenIds',
            'selectedFullSiblingIds',
            'selectedTwinSiblingIds',
            'selectedHalfSiblingIds',
            'roleOptions'
        ));
    }

    public function update(Request $request, Character $character)
    {
        $defaultWorldId = $this->requireCurrentWorldId();
        if (!$defaultWorldId) {
            return back()->withErrors(['world' => "Créez d'abord un monde."])->withInput();
        }

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['nullable', 'string', 'max:120'],
            'family_name' => ['nullable', 'string', 'max:120'],
            'aliases' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', Rule::in(['homme', 'femme', 'autre'])],
            'birth_date' => ['nullable', 'date'],
            'death_date' => ['nullable', 'date', 'after_or_equal:birth_date'],
            'father_id' => ['nullable', 'exists:characters,id', Rule::notIn([$character->id])],
            'mother_id' => ['nullable', 'exists:characters,id', 'different:father_id', Rule::notIn([$character->id])],
            'spouse_id' => ['nullable', 'exists:characters,id', Rule::notIn([$character->id]), 'different:father_id', 'different:mother_id'],
            'ex_character_ids' => ['nullable', 'array'],
            'ex_character_ids.*' => ['nullable', 'exists:characters,id', Rule::notIn([$character->id])],
            'birth_place_id' => ['nullable', 'exists:places,id'],
            'residence_place_id' => ['nullable', 'exists:places,id'],
            'role' => ['required', Rule::in(self::CHARACTER_ROLES)],
            'short_term_goal' => ['nullable', 'string', 'max:3000'],
            'long_term_goal' => ['nullable', 'string', 'max:3000'],
            'secrets' => ['nullable', 'string', 'max:5000'],
            'secrets_is_private' => ['nullable', 'boolean'],
            'has_power' => ['nullable', 'boolean'],
            'power_level' => ['nullable', 'integer', 'min:1', 'max:10'],
            'power_description' => ['nullable', 'string', 'max:3000'],
            'height' => ['nullable', 'string', 'max:120'],
            'hair_eyes' => ['nullable', 'string', 'max:255'],
            'hair_color' => ['nullable', 'string', 'max:120'],
            'eye_color' => ['nullable', 'string', 'max:120'],
            'marks' => ['nullable', 'string', 'max:2000'],
            'clothing_style' => ['nullable', 'string', 'max:2000'],
            'qualities' => ['nullable', 'string', 'max:2000'],
            'flaws' => ['nullable', 'string', 'max:2000'],
            'voice_tics' => ['nullable', 'string', 'max:3000'],
            'voice_audio' => ['nullable', 'file', 'mimetypes:audio/mpeg,audio/wav,audio/ogg,audio/mp4,audio/webm', 'max:20480'],
            'voice_youtube_url' => ['nullable', 'url', 'max:1000', 'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\//i'],
            'summary' => ['nullable', 'string', 'max:3000'],
            'preferred_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'image' => ['nullable', 'image', 'max:4096'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['nullable', 'image', 'max:4096'],
            'gallery_captions' => ['nullable', 'array'],
            'gallery_captions.*' => ['nullable', 'string', 'max:255'],
            'remove_gallery_ids' => ['nullable', 'array'],
            'remove_gallery_ids.*' => ['nullable', 'integer'],
            'relations' => ['nullable', 'array'],
            'relations.*.to_character_id' => ['nullable', 'exists:characters,id', Rule::notIn([$character->id])],
            'relations.*.relation_type' => ['nullable', 'string', 'max:120'],
            'relations.*.description' => ['nullable', 'string', 'max:3000'],
            'relations.*.is_bidirectional' => ['nullable', 'boolean'],
            'items' => ['nullable', 'array'],
            'items.*.name' => ['nullable', 'string', 'max:160'],
            'items.*.rarity' => ['nullable', Rule::in(['commun', 'rare', 'epique', 'legendaire', 'mythique'])],
            'items.*.notes' => ['nullable', 'string', 'max:2000'],
            'jobs' => ['nullable', 'array'],
            'jobs.*.job_id' => ['nullable', Rule::exists('jobs', 'id')],
            'jobs.*.job_name' => ['nullable', 'string', 'max:180'],
            'jobs.*.start_year' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'jobs.*.end_year' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'jobs.*.notes' => ['nullable', 'string', 'max:2000'],
            'educations' => ['nullable', 'array'],
            'educations.*.faction_id' => ['nullable', Rule::exists('factions', 'id')->where('world_id', $defaultWorldId)],
            'educations.*.diploma_id' => ['nullable', 'exists:diplomas,id'],
            'educations.*.field' => ['nullable', 'string', 'max:180'],
            'educations.*.start_year' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'educations.*.end_year' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'educations.*.notes' => ['nullable', 'string', 'max:2000'],
            'species_ids' => ['nullable', 'array'],
            'species_ids.*' => ['nullable', Rule::exists('species', 'id')->where('world_id', $defaultWorldId)],
            'children_link_type' => ['nullable', Rule::in(['father', 'mother'])],
            'children_ids' => ['nullable', 'array'],
            'children_ids.*' => ['nullable', 'exists:characters,id', Rule::notIn([$character->id])],
            'sibling_ids_full' => ['nullable', 'array'],
            'sibling_ids_full.*' => ['nullable', 'exists:characters,id', Rule::notIn([$character->id])],
            'sibling_ids_twin' => ['nullable', 'array'],
            'sibling_ids_twin.*' => ['nullable', 'exists:characters,id', Rule::notIn([$character->id])],
            'sibling_ids_half' => ['nullable', 'array'],
            'sibling_ids_half.*' => ['nullable', 'exists:characters,id', Rule::notIn([$character->id])],
        ]);

        $data['has_children'] = $request->boolean('has_children');
        $data['has_brother_sister'] = $request->boolean('has_brother_sister');
        $data['has_power'] = $request->boolean('has_power');
        $data['secrets_is_private'] = $request->boolean('secrets_is_private');
        $data['name'] = trim($data['first_name'] . ' ' . ($data['last_name'] ?? ''));
        $data['family_name'] = trim((string) ($data['family_name'] ?? '')) !== ''
            ? trim((string) $data['family_name'])
            : (trim((string) ($data['last_name'] ?? '')) ?: null);
        $data['world_id'] = $defaultWorldId;
        $data['preferred_color'] = $this->normalizeHexColor($data['preferred_color'] ?? null);
        $spouseId = isset($data['spouse_id']) && $data['spouse_id'] !== '' ? (int) $data['spouse_id'] : null;
        $selectedExIds = collect($data['ex_character_ids'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values()->all();
        unset($data['spouse_id']);
        unset($data['ex_character_ids']);

        if ($request->hasFile('image')) {
            if ($character->image_path) {
                Storage::disk('public')->delete($character->image_path);
            }
            $data['image_path'] = $request->file('image')->store('characters', 'public');
        }
        $voiceYoutubeUrl = $this->normalizeYoutubeUrl($data['voice_youtube_url'] ?? null);
        $hasVoiceAudio = $request->hasFile('voice_audio');
        if ($hasVoiceAudio && $voiceYoutubeUrl) {
            return back()->withErrors([
                'voice_audio' => 'Choisis soit un fichier audio, soit un lien YouTube.',
                'voice_youtube_url' => 'Choisis soit un fichier audio, soit un lien YouTube.',
            ])->withInput();
        }
        if ($request->hasFile('voice_audio')) {
            if ($character->voice_audio_path) {
                Storage::disk('public')->delete($character->voice_audio_path);
            }
            $data['voice_audio_path'] = $request->file('voice_audio')->store('characters/voices', 'public');
            $data['voice_youtube_url'] = null;
        } else {
            $data['voice_youtube_url'] = $voiceYoutubeUrl;
            if ($voiceYoutubeUrl && $character->voice_audio_path) {
                Storage::disk('public')->delete($character->voice_audio_path);
                $data['voice_audio_path'] = null;
            }
        }

        $relationRows = $data['relations'] ?? [];
        $itemRows = $data['items'] ?? [];
        $jobRows = $data['jobs'] ?? [];
        $educationRows = $data['educations'] ?? [];
        $speciesIds = collect($data['species_ids'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values()->all();
        $childrenLinkType = $data['children_link_type'] ?? 'father';
        $selectedChildrenIds = collect($data['children_ids'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values()->all();
        $siblingIdsByKind = [
            'full' => collect($data['sibling_ids_full'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values()->all(),
            'twin' => collect($data['sibling_ids_twin'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values()->all(),
            'half' => collect($data['sibling_ids_half'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values()->all(),
        ];
        $removeGalleryIds = collect($data['remove_gallery_ids'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values()->all();
        unset($data['relations']);
        unset($data['items'], $data['jobs'], $data['remove_gallery_ids']);
        unset($data['educations']);
        unset($data['species_ids']);
        unset($data['children_link_type'], $data['children_ids'], $data['sibling_ids_full'], $data['sibling_ids_twin'], $data['sibling_ids_half']);
        unset($data['gallery_images'], $data['gallery_captions']);

        $oldFatherId = $character->father_id;
        $oldMotherId = $character->mother_id;
        $oldSpouseId = $character->spouse_id;

        DB::transaction(function () use ($request, $character, $data, $spouseId, $selectedExIds, $relationRows, $itemRows, $jobRows, $educationRows, $speciesIds, $childrenLinkType, $selectedChildrenIds, $siblingIdsByKind, $removeGalleryIds, $oldFatherId, $oldMotherId, $oldSpouseId) {
            $character->update($data);
            $impactedSpouseIds = $this->syncSpouseLink($character, $spouseId);
            $this->syncExPartners($character, $selectedExIds);
            $this->syncOutgoingRelations($character, $relationRows);
            $this->syncCharacterItems($character, $itemRows);
            $this->syncCharacterJobs($character, $jobRows);
            $this->syncCharacterEducations($character, $educationRows);
            $this->syncCharacterSpecies($character, $speciesIds);
            $this->removeCharacterGalleryImages($character, $removeGalleryIds);
            $this->addCharacterGalleryImages($character, $request->file('gallery_images', []), $request->input('gallery_captions', []));
            $this->syncChildrenLinks($character, (bool) $data['has_children'], $childrenLinkType, $selectedChildrenIds);
            $this->syncSiblingRelations($character, (bool) $data['has_brother_sister'], $siblingIdsByKind);

            $this->syncAutoFamilyRelationsForCharacters(array_filter([
                $character->id,
                $oldFatherId,
                $oldMotherId,
                $oldSpouseId,
                $character->father_id,
                $character->mother_id,
                ...$impactedSpouseIds,
            ]));
        });

        return redirect()->route('manage.characters.index')->with('success', 'Personnage mis à jour avec ses relations.');
    }

    public function destroy(Character $character)
    {
        if ($character->image_path) {
            Storage::disk('public')->delete($character->image_path);
        }
        if ($character->voice_audio_path) {
            Storage::disk('public')->delete($character->voice_audio_path);
        }

        $galleryPaths = $character->galleryImages()->pluck('image_path')->all();
        if (!empty($galleryPaths)) {
            Storage::disk('public')->delete($galleryPaths);
        }

        $character->delete();

        return redirect()->route('manage.characters.index')->with('success', 'Personnage supprimé.');
    }

    private function syncOutgoingRelations(Character $character, array $relationRows): void
    {
        $character->outgoingRelations()->delete();

        foreach ($relationRows as $row) {
            $toId = (int) ($row['to_character_id'] ?? 0);
            $type = trim((string) ($row['relation_type'] ?? ''));

            if ($toId <= 0 || $type === '' || $toId === (int) $character->id) {
                continue;
            }

            CharacterRelation::create([
                'from_character_id' => $character->id,
                'to_character_id' => $toId,
                'relation_type' => $type,
                'relation_category' => $this->inferRelationMetadata($type)[0],
                'sibling_kind' => $this->inferRelationMetadata($type)[1],
                'description' => trim((string) ($row['description'] ?? '')) ?: null,
                'is_bidirectional' => (bool) ($row['is_bidirectional'] ?? true),
            ]);
        }
    }

    private function syncChildrenLinks(Character $character, bool $hasChildren, string $linkType, array $selectedChildrenIds): void
    {
        Character::where('father_id', $character->id)->update(['father_id' => null]);
        Character::where('mother_id', $character->id)->update(['mother_id' => null]);

        if (!$hasChildren || empty($selectedChildrenIds)) {
            return;
        }

        $column = $linkType === 'mother' ? 'mother_id' : 'father_id';

        Character::whereIn('id', $selectedChildrenIds)->update([$column => $character->id]);
    }

    private function syncSiblingRelations(Character $character, bool $hasSiblings, array $siblingsByKind): void
    {
        $characterId = (int) $character->id;
        $kinds = ['full', 'twin', 'half'];
        $normalized = [];
        foreach ($kinds as $kind) {
            $normalized[$kind] = collect($siblingsByKind[$kind] ?? [])
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0 && $id !== $characterId)
                ->unique()
                ->values()
                ->all();
        }

        CharacterRelation::query()
            ->where(function ($query) use ($characterId) {
                $query->where('from_character_id', $characterId)
                    ->orWhere('to_character_id', $characterId);
            })
            ->where('description', 'like', self::AUTO_SIBLING_TAG . '%')
            ->delete();

        if (!$hasSiblings) {
            return;
        }

        $priority = ['full' => 1, 'half' => 2, 'twin' => 3];
        $resolvedKindsBySiblingId = [];
        foreach ($normalized as $kind => $ids) {
            foreach ($ids as $id) {
                if (!isset($resolvedKindsBySiblingId[$id]) || $priority[$kind] > $priority[$resolvedKindsBySiblingId[$id]]) {
                    $resolvedKindsBySiblingId[$id] = $kind;
                }
            }
        }

        if (empty($resolvedKindsBySiblingId)) {
            return;
        }

        $siblings = Character::query()
            ->whereIn('id', array_keys($resolvedKindsBySiblingId))
            ->get(['id', 'gender']);

        foreach ($siblings as $sibling) {
            $kind = $resolvedKindsBySiblingId[(int) $sibling->id] ?? 'full';
            $fromType = $this->siblingRelationTypeFromKindGender($kind, (string) $character->gender);
            $toType = $this->siblingRelationTypeFromKindGender($kind, (string) $sibling->gender);
            $description = self::AUTO_SIBLING_TAG . ' ' . $kind;

            CharacterRelation::updateOrCreate(
                [
                    'from_character_id' => $characterId,
                    'to_character_id' => (int) $sibling->id,
                    'description' => $description,
                ],
                [
                    'relation_type' => $fromType,
                    'relation_category' => 'family_sibling',
                    'sibling_kind' => $kind,
                    'intensity' => 9,
                    'is_bidirectional' => false,
                ]
            );

            CharacterRelation::updateOrCreate(
                [
                    'from_character_id' => (int) $sibling->id,
                    'to_character_id' => $characterId,
                    'description' => $description,
                ],
                [
                    'relation_type' => $toType,
                    'relation_category' => 'family_sibling',
                    'sibling_kind' => $kind,
                    'intensity' => 9,
                    'is_bidirectional' => false,
                ]
            );
        }
    }

    private function syncSpouseLink(Character $character, ?int $spouseId): array
    {
        $spouseId = $spouseId ?: null;
        if ($spouseId === (int) $character->id) {
            $spouseId = null;
        }

        $character->refresh();
        $impacted = collect([(int) $character->id]);
        $currentSpouseId = $character->spouse_id ? (int) $character->spouse_id : null;

        if ($currentSpouseId && $currentSpouseId !== $spouseId) {
            Character::where('id', $currentSpouseId)
                ->where('spouse_id', $character->id)
                ->update(['spouse_id' => null]);
            $impacted->push($currentSpouseId);
        }

        if (!$spouseId) {
            $character->update(['spouse_id' => null]);
            return $impacted->unique()->values()->all();
        }

        $spouse = Character::find($spouseId);
        if (!$spouse) {
            $character->update(['spouse_id' => null]);
            return $impacted->unique()->values()->all();
        }

        if ($spouse->spouse_id && (int) $spouse->spouse_id !== (int) $character->id) {
            Character::where('id', (int) $spouse->spouse_id)
                ->where('spouse_id', $spouse->id)
                ->update(['spouse_id' => null]);
            $impacted->push((int) $spouse->spouse_id);
        }

        Character::where('spouse_id', $character->id)
            ->where('id', '!=', $spouse->id)
            ->update(['spouse_id' => null]);

        Character::where('spouse_id', $spouse->id)
            ->where('id', '!=', $character->id)
            ->update(['spouse_id' => null]);

        $character->update(['spouse_id' => $spouse->id]);
        $spouse->update(['spouse_id' => $character->id]);
        $impacted->push((int) $spouse->id);

        return $impacted->unique()->values()->all();
    }

    private function syncExPartners(Character $character, array $selectedExIds): void
    {
        $characterId = (int) $character->id;
        $currentSpouseId = (int) ($character->spouse_id ?? 0);

        $normalizedIds = collect($selectedExIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0 && $id !== $characterId && $id !== $currentSpouseId)
            ->unique()
            ->values();

        $validExIds = Character::query()
            ->whereIn('id', $normalizedIds->all())
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        DB::table('character_exes')
            ->where('character_id', $characterId)
            ->orWhere('ex_character_id', $characterId)
            ->delete();

        if (empty($validExIds)) {
            return;
        }

        $now = now();
        $rows = [];
        foreach ($validExIds as $exId) {
            $rows[] = [
                'character_id' => $characterId,
                'ex_character_id' => $exId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $rows[] = [
                'character_id' => $exId,
                'ex_character_id' => $characterId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('character_exes')->insert($rows);
    }

    private function syncAutoFamilyRelationsForCharacters(array $characterIds): void
    {
        $ids = collect($characterIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        foreach ($ids as $id) {
            $this->syncAutoFamilyRelationsForCharacter((int) $id);
        }
    }

    private function syncAutoFamilyRelationsForCharacter(int $characterId): void
    {
        $character = Character::find($characterId);
        if (!$character) {
            return;
        }

        CharacterRelation::query()
            ->where(function ($query) use ($characterId) {
                $query->where('from_character_id', $characterId)
                    ->orWhere('to_character_id', $characterId);
            })
            ->where('description', 'like', self::AUTO_FAMILY_TAG . '%')
            ->delete();

        $children = Character::query()
            ->where('father_id', $characterId)
            ->orWhere('mother_id', $characterId)
            ->get(['id', 'gender', 'father_id', 'mother_id']);

        foreach ($children as $child) {
            $parentType = ((int) $child->father_id === $characterId) ? 'père' : 'mère';
            $childType = 'fils/fille';
            if ((string) $child->gender === 'femme') {
                $childType = 'fille';
            } elseif ((string) $child->gender === 'homme') {
                $childType = 'fils';
            }

            CharacterRelation::updateOrCreate(
                [
                    'from_character_id' => $characterId,
                    'to_character_id' => $child->id,
                    'relation_type' => $parentType,
                    'description' => self::AUTO_FAMILY_TAG . ' parent->enfant',
                ],
                [
                    'relation_category' => $this->inferRelationMetadata($parentType)[0],
                    'sibling_kind' => $this->inferRelationMetadata($parentType)[1],
                    'intensity' => 10,
                    'is_bidirectional' => false,
                ]
            );

            CharacterRelation::updateOrCreate(
                [
                    'from_character_id' => $child->id,
                    'to_character_id' => $characterId,
                    'relation_type' => $childType,
                    'description' => self::AUTO_FAMILY_TAG . ' enfant->parent',
                ],
                [
                    'relation_category' => $this->inferRelationMetadata($childType)[0],
                    'sibling_kind' => $this->inferRelationMetadata($childType)[1],
                    'intensity' => 10,
                    'is_bidirectional' => false,
                ]
            );
        }

        if ($character->spouse_id) {
            $spouse = Character::find((int) $character->spouse_id);
            if ($spouse) {
                $characterToSpouseType = $this->relationTypeFromGender((string) $character->gender, 'spouse');
                $spouseToCharacterType = $this->relationTypeFromGender((string) $spouse->gender, 'spouse');

                CharacterRelation::updateOrCreate(
                    [
                        'from_character_id' => $character->id,
                        'to_character_id' => $spouse->id,
                        'relation_type' => $characterToSpouseType,
                        'description' => self::AUTO_FAMILY_TAG . ' spouse',
                    ],
                    [
                        'relation_category' => $this->inferRelationMetadata($characterToSpouseType)[0],
                        'sibling_kind' => $this->inferRelationMetadata($characterToSpouseType)[1],
                        'intensity' => 10,
                        'is_bidirectional' => false,
                    ]
                );

                CharacterRelation::updateOrCreate(
                    [
                        'from_character_id' => $spouse->id,
                        'to_character_id' => $character->id,
                        'relation_type' => $spouseToCharacterType,
                        'description' => self::AUTO_FAMILY_TAG . ' spouse',
                    ],
                    [
                        'relation_category' => $this->inferRelationMetadata($spouseToCharacterType)[0],
                        'sibling_kind' => $this->inferRelationMetadata($spouseToCharacterType)[1],
                        'intensity' => 10,
                        'is_bidirectional' => false,
                    ]
                );
            }
        }
    }

    private function relationTypeFromGender(string $gender, string $group): string
    {
        if ($group === 'spouse') {
            if ($gender === 'femme') {
                return 'epouse';
            }
            if ($gender === 'homme') {
                return 'epoux';
            }
            return 'epoux/epouse';
        }

        return '-';
    }

    private function inferRelationMetadata(string $relationType): array
    {
        $type = mb_strtolower(trim($relationType));
        $category = 'custom';
        $siblingKind = null;

        if (in_array($type, ['pere', 'mere', 'fils', 'fille', 'fils/fille', 'parent de', 'enfant de'], true)) {
            $category = 'family_lineage';
        } elseif (in_array($type, ['frere', 'soeur', 'frere/soeur'], true)) {
            $category = 'family_sibling';
            $siblingKind = 'full';
        } elseif (in_array($type, ['demi-frere', 'demi-soeur', 'demi-frere/soeur'], true)) {
            $category = 'family_sibling';
            $siblingKind = 'half';
        } elseif (in_array($type, ['jumeau', 'jumelle', 'jumeaux'], true)) {
            $category = 'family_sibling';
            $siblingKind = 'twin';
        } elseif (in_array($type, ['epoux', 'epouse', 'epoux/epouse', 'amour'], true)) {
            $category = 'family_couple';
        } elseif (in_array($type, ['ami', 'allie', 'ennemi', 'mentor', 'rival'], true)) {
            $category = 'social';
        }

        return [$category, $siblingKind];
    }

    private function siblingRelationTypeFromKindGender(string $kind, string $gender): string
    {
        if ($kind === 'twin') {
            if ($gender === 'femme') {
                return 'jumelle';
            }
            if ($gender === 'homme') {
                return 'jumeau';
            }

            return 'jumeaux';
        }

        if ($kind === 'half') {
            if ($gender === 'femme') {
                return 'demi-soeur';
            }
            if ($gender === 'homme') {
                return 'demi-frere';
            }

            return 'demi-frere/soeur';
        }

        if ($gender === 'femme') {
            return 'soeur';
        }
        if ($gender === 'homme') {
            return 'frere';
        }

        return 'frere/soeur';
    }

    private function siblingKindFromRelationType(string $relationType): string
    {
        $type = mb_strtolower(trim($relationType));
        if (in_array($type, ['jumeau', 'jumelle', 'jumeaux'], true)) {
            return 'twin';
        }
        if (in_array($type, ['demi-frere', 'demi-soeur', 'demi-frere/soeur'], true)) {
            return 'half';
        }

        return 'full';
    }

    private function syncCharacterItems(Character $character, array $rows): void
    {
        $character->items()->delete();

        foreach ($rows as $row) {
            $name = trim((string) ($row['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            CharacterItem::create([
                'character_id' => $character->id,
                'name' => $name,
                'rarity' => $row['rarity'] ?: null,
                'notes' => trim((string) ($row['notes'] ?? '')) ?: null,
            ]);
        }
    }

    private function syncCharacterJobs(Character $character, array $rows): void
    {
        $character->jobs()->delete();

        $jobIds = collect($rows)
            ->map(fn ($row) => (int) ($row['job_id'] ?? 0))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $jobsById = Job::query()
            ->whereIn('id', $jobIds)
            ->get(['id', 'name'])
            ->keyBy('id');

        $jobNames = collect($rows)
            ->map(fn ($row) => trim((string) ($row['job_name'] ?? '')))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $jobsByName = Job::query()
            ->whereIn('name', $jobNames)
            ->get(['id', 'name'])
            ->keyBy(fn ($job) => mb_strtolower($job->name));

        foreach ($rows as $row) {
            $jobId = (int) ($row['job_id'] ?? 0);
            $jobName = trim((string) ($row['job_name'] ?? ''));

            if ($jobId > 0 && $jobsById->has($jobId)) {
                $jobName = (string) $jobsById->get($jobId)->name;
            } elseif ($jobName !== '') {
                $matched = $jobsByName->get(mb_strtolower($jobName));
                if ($matched) {
                    $jobId = (int) $matched->id;
                    $jobName = (string) $matched->name;
                }
            }

            if ($jobName === '') {
                continue;
            }

            $startYear = isset($row['start_year']) && $row['start_year'] !== '' ? (int) $row['start_year'] : null;
            $endYear = isset($row['end_year']) && $row['end_year'] !== '' ? (int) $row['end_year'] : null;

            if ($startYear !== null && $endYear !== null && $endYear < $startYear) {
                $tmp = $startYear;
                $startYear = $endYear;
                $endYear = $tmp;
            }

            CharacterJob::create([
                'character_id' => $character->id,
                'job_id' => $jobId ?: null,
                'job_name' => $jobName,
                'start_year' => $startYear,
                'end_year' => $endYear,
                'notes' => trim((string) ($row['notes'] ?? '')) ?: null,
            ]);
        }
    }

    private function syncCharacterEducations(Character $character, array $rows): void
    {
        $character->educations()->delete();

        $diplomaIds = collect($rows)
            ->map(fn ($row) => (int) ($row['diploma_id'] ?? 0))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $diplomasById = Diploma::query()
            ->whereIn('id', $diplomaIds)
            ->get(['id', 'faction_id'])
            ->keyBy('id');

        foreach ($rows as $row) {
            $factionId = (int) ($row['faction_id'] ?? 0);
            if ($factionId <= 0) {
                continue;
            }

            $startYear = isset($row['start_year']) && $row['start_year'] !== '' ? (int) $row['start_year'] : null;
            $endYear = isset($row['end_year']) && $row['end_year'] !== '' ? (int) $row['end_year'] : null;
            if ($startYear !== null && $endYear !== null && $endYear < $startYear) {
                $tmp = $startYear;
                $startYear = $endYear;
                $endYear = $tmp;
            }

            $diplomaId = (int) ($row['diploma_id'] ?? 0);
            if ($diplomaId > 0) {
                $diploma = $diplomasById->get($diplomaId);
                if (!$diploma || (int) $diploma->faction_id !== $factionId) {
                    $diplomaId = 0;
                }
            }

            CharacterEducation::create([
                'character_id' => $character->id,
                'faction_id' => $factionId,
                'diploma_id' => $diplomaId ?: null,
                'field' => trim((string) ($row['field'] ?? '')) ?: null,
                'start_year' => $startYear,
                'end_year' => $endYear,
                'notes' => trim((string) ($row['notes'] ?? '')) ?: null,
            ]);
        }
    }

    private function syncCharacterSpecies(Character $character, array $speciesIds): void
    {
        $character->species()->sync($speciesIds);
    }

    private function removeCharacterGalleryImages(Character $character, array $ids): void
    {
        if (empty($ids)) {
            return;
        }

        $images = $character->galleryImages()->whereIn('id', $ids)->get();
        foreach ($images as $image) {
            if ($image->image_path) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        }
    }

    private function addCharacterGalleryImages(Character $character, array $uploadedFiles, array $captions): void
    {
        if (empty($uploadedFiles)) {
            return;
        }

        $startOrder = (int) ($character->galleryImages()->max('sort_order') ?? 0);

        foreach ($uploadedFiles as $index => $file) {
            if (!$file) {
                continue;
            }

            $path = $file->store('characters/gallery', 'public');
            $startOrder++;

            CharacterGalleryImage::create([
                'character_id' => $character->id,
                'image_path' => $path,
                'caption' => trim((string) ($captions[$index] ?? '')) ?: null,
                'sort_order' => $startOrder,
            ]);
        }
    }

    private function buildDataUriFromPublicDisk(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (!Storage::disk('public')->exists($path)) {
            return null;
        }

        $absolutePath = Storage::disk('public')->path($path);
        $mime = @mime_content_type($absolutePath) ?: 'image/jpeg';
        $content = @file_get_contents($absolutePath);

        if ($content === false) {
            return null;
        }

        return 'data:' . $mime . ';base64,' . base64_encode($content);
    }

    private function normalizeHexColor(?string $value): ?string
    {
        $color = trim((string) $value);
        if ($color === '') {
            return null;
        }

        if ($color[0] !== '#') {
            $color = '#' . $color;
        }

        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            return null;
        }

        return strtoupper($color);
    }

    private function normalizeYoutubeUrl(?string $value): ?string
    {
        $url = trim((string) $value);
        if ($url === '') {
            return null;
        }

        if (!preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        return $url;
    }
}


