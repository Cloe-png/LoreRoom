<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\CharacterEducation;
use App\Models\CharacterEvent;
use App\Models\CharacterGalleryImage;
use App\Models\CharacterItem;
use App\Models\CharacterJob;
use App\Models\CharacterPower;
use App\Models\CharacterOutfitImage;
use App\Models\CharacterRelation;
use App\Models\Chapter;
use App\Models\Chronicle;
use App\Models\Diploma;
use App\Models\Faction;
use App\Models\FactionMembership;
use App\Models\FactionRelation;
use App\Models\Job;
use App\Models\LoreEntry;
use App\Models\Place;
use App\Models\PlaceGalleryImage;
use App\Models\Species;
use App\Models\World;
use App\Models\UserLog;
use App\Support\PasswordRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;
use ZipArchive;

class AccountSettingsController extends Controller
{
    public function edit(Request $request): View
    {
        $localeOptions = collect(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales())
            ->only(['fr', 'en'])
            ->mapWithKeys(function (array $info, string $locale) {
                return [$locale => $info['native'] ?? $info['name'] ?? strtoupper($locale)];
            })
            ->all();

        return view('manage.account.edit', [
            'user' => $request->user(),
            'passwordHelp' => PasswordRules::helpText(),
            'localeOptions' => $localeOptions,
            'roleOptions' => config('module_access.signup_roles', []),
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => PasswordRules::defaultsWithConfirmation(),
        ], [
            'password.min' => __('app.auth.password_min'),
            'password.regex' => __('app.auth.password_regex'),
            'password.confirmed' => __('app.auth.password_confirmed'),
        ]);

        if (!$user || !Hash::check((string) $data['current_password'], (string) $user->password)) {
            return back()->withErrors([
                'current_password' => __('app.auth.password_current_invalid'),
            ]);
        }

        $user->forceFill([
            'password' => Hash::make((string) $data['password']),
            'failed_login_attempts' => 0,
            'last_failed_login_at' => null,
            'locked_at' => null,
            'password_reset_pending_at' => null,
            'login_token_hash' => null,
            'login_token_expires_at' => null,
            'remember_token' => null,
        ])->save();

        UserLog::logAction((int) $user->id, 'password_changed', 'manage.account.password', 'self');

        return redirect()
            ->route('manage.account.edit')
            ->with('success', __('app.account.password_updated'));
    }

    public function updateLocale(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'locale' => ['required', 'string', Rule::in(array_keys(LaravelLocalization::getSupportedLocales()))],
        ]);

        $user = $request->user();
        if ($user) {
            $user->forceFill([
                'preferred_locale' => $data['locale'],
            ])->save();
        }

        $request->session()->put('locale', $data['locale']);

        return redirect()
            ->route('manage.account.edit')
            ->with('success', __('app.account.language_updated'));
    }

    public function updateRoles(Request $request): RedirectResponse
    {
        $allowedRoles = array_keys(config('module_access.signup_roles', []));

        $data = $request->validate([
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['required', 'string', Rule::in($allowedRoles)],
        ]);

        $user = $request->user();
        if ($user) {
            $currentRoles = array_values(array_filter(
                array_map('strval', $user->roles ?? []),
                fn (string $role) => !in_array($role, $allowedRoles, true)
            ));

            $selectedRoles = array_values(array_unique(array_map('strval', $data['roles'])));

            $user->forceFill([
                'roles' => array_values(array_unique(array_merge($currentRoles, $selectedRoles))),
            ])->save();

            UserLog::logAction((int) $user->id, 'roles_updated', 'manage.account.roles', 'self');
        }

        return redirect()
            ->route('manage.account.edit')
            ->with('success', __('app.account_roles.updated'));
    }

    public function exportWorld(Request $request)
    {
        $user = $request->user();
        $world = $this->currentWorld();

        if (!$user || !$world || (int) $world->user_id !== (int) $user->id) {
            return redirect()
                ->route('manage.account.edit')
                ->withErrors(['world_archive' => __('app.account.export_unavailable')]);
        }

        $exportedAt = now();
        $payload = $this->buildWorldExportPayload($world, $exportedAt);
        $tmpDir = storage_path('app/tmp_exports');
        if (!File::isDirectory($tmpDir)) {
            File::makeDirectory($tmpDir, 0755, true);
        }

        $slug = Str::slug((string) $world->name);
        $baseName = 'loreroom-world-' . ($slug !== '' ? $slug : 'world') . '-' . $exportedAt->format('Ymd-His');
        $zipPath = $tmpDir . DIRECTORY_SEPARATOR . $baseName . '.zip';

        if (class_exists(ZipArchive::class)) {
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                $zip->addFromString(
                    'world.json',
                    json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                );

                foreach ($payload['assets'] as $asset) {
                    $sourcePath = Storage::disk('public')->path($asset['path']);
                    if (is_file($sourcePath)) {
                        $zip->addFile($sourcePath, 'assets/' . ltrim(str_replace('\\', '/', $asset['path']), '/'));
                    }
                }

                $zip->close();

                return response()->download($zipPath, $baseName . '.zip')->deleteFileAfterSend(true);
            }
        }

        $jsonPath = $tmpDir . DIRECTORY_SEPARATOR . $baseName . '.json';
        File::put($jsonPath, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return response()->download($jsonPath, $baseName . '.json')->deleteFileAfterSend(true);
    }

    public function importWorld(Request $request): RedirectResponse
    {
        $request->validate([
            'world_archive' => ['required', 'file', 'mimes:zip,json', 'max:102400'],
        ]);

        $user = $request->user();
        if (!$user) {
            abort(403);
        }

        $uploaded = $request->file('world_archive');
        if (!$uploaded instanceof UploadedFile) {
            return back()->withErrors(['world_archive' => __('app.account.import_invalid')]);
        }

        $extension = strtolower((string) $uploaded->getClientOriginalExtension());
        if (!in_array($extension, ['zip', 'json'], true)) {
            return back()->withErrors(['world_archive' => __('app.account.import_invalid_extension')]);
        }
        $tempDir = null;

        try {
            [$payload, $assetsDir, $tempDir] = $this->readImportPayload($uploaded);
            $world = $this->importPayloadAsWorld((int) $user->id, $payload, $assetsDir);

            $user->forceFill(['current_world_id' => (int) $world->id])->save();
            $request->session()->put('selected_world_id', (int) $world->id);

            UserLog::logAction((int) $user->id, 'world_imported', 'manage.account.import-world', 'world:' . (int) $world->id);

            return redirect()
                ->route('manage.worlds.show', $world)
                ->with('success', __('app.account.import_success', ['world' => $world->name]));
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors(['world_archive' => __('app.account.import_failed')]);
        } finally {
            if ($tempDir && File::isDirectory($tempDir)) {
                File::deleteDirectory($tempDir);
            }
        }
    }

    private function buildWorldExportPayload(World $world, Carbon $exportedAt): array
    {
        $characterIds = Character::withoutGlobalScopes()
            ->where('world_id', $world->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $placeIds = Place::withoutGlobalScopes()
            ->where('world_id', $world->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $factionIds = Faction::withoutGlobalScopes()
            ->where('world_id', $world->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $chronicleIds = Chronicle::withoutGlobalScopes()
            ->where('world_id', $world->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $chapterIds = Chapter::withoutGlobalScopes()
            ->where('world_id', $world->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $speciesIds = Species::withoutGlobalScopes()
            ->where('world_id', $world->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $datasets = [
            'worlds' => [$this->normalizeRecord(World::query()->findOrFail($world->id)->toArray())],
            'characters' => $this->exportModelRows(Character::withoutGlobalScopes()->where('world_id', $world->id)->orderBy('id')->get()),
            'character_events' => $this->exportModelRows(CharacterEvent::query()->whereIn('character_id', $characterIds)->orderBy('id')->get()),
            'character_gallery_images' => $this->exportModelRows(CharacterGalleryImage::query()->whereIn('character_id', $characterIds)->orderBy('id')->get()),
            'character_powers' => $this->exportModelRows(CharacterPower::query()->whereIn('character_id', $characterIds)->orderBy('id')->get()),
            'character_outfit_images' => $this->exportModelRows(CharacterOutfitImage::query()->whereIn('character_id', $characterIds)->orderBy('id')->get()),
            'character_items' => $this->exportModelRows(CharacterItem::query()->whereIn('character_id', $characterIds)->orderBy('id')->get()),
            'character_jobs' => $this->exportModelRows(CharacterJob::query()->whereIn('character_id', $characterIds)->orderBy('id')->get()),
            'character_educations' => $this->exportModelRows(CharacterEducation::query()->whereIn('character_id', $characterIds)->orderBy('id')->get()),
            'character_relations' => $this->exportModelRows(
                CharacterRelation::query()
                    ->whereIn('from_character_id', $characterIds)
                    ->whereIn('to_character_id', $characterIds)
                    ->orderBy('id')
                    ->get()
            ),
            'character_exes' => $this->normalizeDbRows(
                DB::table('character_exes')
                    ->whereIn('character_id', $characterIds)
                    ->whereIn('ex_character_id', $characterIds)
                    ->orderBy('id')
                    ->get()
            ),
            'places' => collect($this->exportModelRows(Place::withoutGlobalScopes()->where('world_id', $world->id)->orderBy('id')->get()))
                ->map(function (array $place) { unset($place['private_notes']); return $place; })
                ->values()->all(),
            'place_gallery_images' => $this->exportModelRows(PlaceGalleryImage::query()->whereIn('place_id', $placeIds)->orderBy('id')->get()),
            'chronicles' => $this->exportModelRows(Chronicle::withoutGlobalScopes()->where('world_id', $world->id)->orderBy('id')->get()),
            'chapters' => $this->exportModelRows(Chapter::withoutGlobalScopes()->where('world_id', $world->id)->orderBy('id')->get()),
            'chapter_character' => $this->normalizeDbRows(
                DB::table('chapter_character')
                    ->whereIn('chapter_id', $chapterIds)
                    ->whereIn('character_id', $characterIds)
                    ->orderBy('chapter_id')
                    ->orderBy('character_id')
                    ->get()
            ),
            'chapter_place' => $this->normalizeDbRows(
                DB::table('chapter_place')
                    ->whereIn('chapter_id', $chapterIds)
                    ->whereIn('place_id', $placeIds)
                    ->orderBy('chapter_id')
                    ->orderBy('place_id')
                    ->get()
            ),
            'chapter_chronicle' => $this->normalizeDbRows(
                DB::table('chapter_chronicle')
                    ->whereIn('chapter_id', $chapterIds)
                    ->whereIn('chronicle_id', $chronicleIds)
                    ->orderBy('chapter_id')
                    ->orderBy('chronicle_id')
                    ->get()
            ),
            'chronicle_character' => $this->normalizeDbRows(
                DB::table('chronicle_character')
                    ->whereIn('chronicle_id', $chronicleIds)
                    ->whereIn('character_id', $characterIds)
                    ->orderBy('chronicle_id')
                    ->orderBy('character_id')
                    ->get()
            ),
            'factions' => $this->exportModelRows(Faction::withoutGlobalScopes()->where('world_id', $world->id)->orderBy('id')->get()),
            'faction_memberships' => $this->exportModelRows(FactionMembership::query()->whereIn('faction_id', $factionIds)->orderBy('id')->get()),
            'faction_relations' => $this->exportModelRows(
                FactionRelation::query()
                    ->whereIn('faction_id', $factionIds)
                    ->whereIn('related_faction_id', $factionIds)
                    ->orderBy('id')
                    ->get()
            ),
            'diplomas' => $this->exportModelRows(Diploma::query()->whereIn('faction_id', $factionIds)->orderBy('id')->get()),
            'lore_entries' => $this->exportModelRows(LoreEntry::withoutGlobalScopes()->where('world_id', $world->id)->orderBy('id')->get()),
            'species' => $this->exportModelRows(Species::withoutGlobalScopes()->where('world_id', $world->id)->orderBy('id')->get()),
            'character_species' => $this->normalizeDbRows(
                DB::table('character_species')
                    ->whereIn('character_id', $characterIds)
                    ->whereIn('species_id', $speciesIds)
                    ->orderBy('character_id')
                    ->orderBy('species_id')
                    ->get()
            ),
            'jobs' => $this->exportModelRows(Job::query()->where(function ($query) use ($world) {
                $query->whereNull('world_id')->orWhere('world_id', $world->id);
            })->orderBy('id')->get()),
        ];

        $assets = $this->collectExportAssets($datasets);
        $references = $this->buildExportReferences($datasets);

        return [
            'meta' => [
                'format' => 'loreroom-world-export',
                'version' => 2,
                'exported_at' => $exportedAt->toIso8601String(),
                'locale' => app()->getLocale(),
                'schema_visibility' => 'public',
            ],
            'world' => [
                'ref' => $this->resolveExportReference($references, 'worlds', $world->id),
                'name' => (string) $world->name,
                'slug' => (string) $world->slug,
            ],
            'content' => $this->buildPublicExportCollections($datasets, $references),
            'assets' => $assets,
        ];
    }

    private function exportModelRows($rows): array
    {
        return collect($rows)
            ->map(fn ($row) => $this->normalizeRecord($row->toArray()))
            ->values()
            ->all();
    }

    private function normalizeDbRows($rows): array
    {
        return collect($rows)
            ->map(fn ($row) => $this->normalizeRecord((array) $row))
            ->values()
            ->all();
    }

    private function normalizeRecord(array $record): array
    {
        foreach ($record as $key => $value) {
            if ($value instanceof Carbon) {
                $record[$key] = $value->toIso8601String();
                continue;
            }

            if (is_array($value)) {
                $record[$key] = $this->normalizeRecord($value);
            }
        }

        return $record;
    }

    private function collectExportAssets(array $tables): array
    {
        $keys = ['image_path', 'voice_audio_path', 'logo_path'];
        $assets = collect($tables)
            ->flatMap(function (array $rows) use ($keys) {
                return collect($rows)->flatMap(function (array $row) use ($keys) {
                    return collect($keys)
                        ->map(fn ($key) => isset($row[$key]) ? trim((string) $row[$key]) : '')
                        ->filter();
                });
            })
            ->map(function (string $path) {
                return [
                    'path' => $path,
                    'exists' => Storage::disk('public')->exists($path),
                ];
            })
            ->unique('path')
            ->values()
            ->all();

        return $assets;
    }

    private function readImportPayload(UploadedFile $uploaded): array
    {
        $extension = strtolower((string) $uploaded->getClientOriginalExtension());

        if ($extension === 'json') {
            $raw = (string) File::get($uploaded->getRealPath());
            $payload = json_decode($raw, true);
            if (!is_array($payload)) {
                throw new \RuntimeException('Invalid JSON payload');
            }

            return [$payload, null, null];
        }

        $tmpRoot = storage_path('app/tmp_imports');
        if (!File::isDirectory($tmpRoot)) {
            File::makeDirectory($tmpRoot, 0755, true);
        }

        $tmpDir = $tmpRoot . DIRECTORY_SEPARATOR . Str::uuid()->toString();
        File::makeDirectory($tmpDir, 0755, true);

        $zip = new ZipArchive();
        if ($zip->open($uploaded->getRealPath()) !== true) {
            throw new \RuntimeException('Unable to open zip archive');
        }

        $zip->extractTo($tmpDir);
        $zip->close();

        $jsonPath = $tmpDir . DIRECTORY_SEPARATOR . 'world.json';
        if (!File::exists($jsonPath)) {
            throw new \RuntimeException('world.json missing in archive');
        }

        $payload = json_decode((string) File::get($jsonPath), true);
        if (!is_array($payload)) {
            throw new \RuntimeException('Invalid JSON payload');
        }

        $assetsDir = $tmpDir . DIRECTORY_SEPARATOR . 'assets';
        return [$payload, File::isDirectory($assetsDir) ? $assetsDir : null, $tmpDir];
    }

    private function importPayloadAsWorld(int $userId, array $payload, ?string $assetsDir): World
    {
        $collections = $this->resolveImportCollections($payload);
        $worldName = trim((string) data_get($payload, 'world.name', 'Monde importé'));
        if ($worldName === '') {
            $worldName = 'Monde importé';
        }

        return DB::transaction(function () use ($userId, $worldName, $collections, $assetsDir, $payload) {
            $world = World::create([
                'user_id' => $userId,
                'name' => $worldName,
                'slug' => $this->uniqueImportWorldSlug($worldName),
                'summary' => null,
                'status' => 'active',
            ]);

            $assetMap = $this->importAssetsFromArchive($payload, $assetsDir, (int) $world->id);
            $refMap = [];
            $createdRows = [];
            $definitions = $this->importDatasetDefinitions();

            foreach ($definitions as $definition) {
                $collectionKey = $definition['public_key'];
                $rows = $collections[$collectionKey] ?? [];
                if (!is_array($rows)) {
                    continue;
                }

                foreach ($rows as $row) {
                    if (!is_array($row)) {
                        continue;
                    }

                    $prepared = $this->prepareImportInsertRow($row, $definition, (int) $world->id, $assetMap);
                    $id = DB::table($definition['table'])->insertGetId($prepared);
                    $createdRows[$collectionKey][] = ['id' => (int) $id, 'row' => $row];

                    $ref = isset($row['ref']) ? trim((string) $row['ref']) : '';
                    if ($ref !== '') {
                        $refMap[$collectionKey][$ref] = (int) $id;
                    }
                }
            }

            foreach ($definitions as $definition) {
                $collectionKey = $definition['public_key'];
                $rows = $createdRows[$collectionKey] ?? [];
                foreach ($rows as $entry) {
                    $updates = $this->resolveImportForeignKeys((array) $entry['row'], $refMap);
                    if ($updates !== []) {
                        DB::table($definition['table'])->where('id', (int) $entry['id'])->update($updates);
                    }
                }
            }

            return $world;
        });
    }

    private function resolveImportCollections(array $payload): array
    {
        if (is_array($payload['content'] ?? null)) {
            return $payload['content'];
        }

        if (!is_array($payload['tables'] ?? null)) {
            throw new \RuntimeException('Unsupported import format');
        }

        $collections = [];
        foreach ($this->exportDatasetDefinitions() as $datasetKey => $definition) {
            $collections[$definition['public_key']] = is_array($payload['tables'][$datasetKey] ?? null)
                ? $payload['tables'][$datasetKey]
                : [];
        }

        return $collections;
    }

    private function prepareImportInsertRow(array $row, array $definition, int $worldId, array $assetMap): array
    {
        $prepared = [];

        foreach ($row as $key => $value) {
            if ($key === 'id' || $key === 'ref' || str_ends_with($key, '_ref')) {
                continue;
            }

            if (in_array($key, ['created_at', 'updated_at'], true) && $value === '') {
                $value = null;
            }

            if (in_array($key, ['image_path', 'voice_audio_path', 'logo_path'], true) && is_string($value)) {
                $trimmed = trim($value);
                if ($trimmed !== '' && isset($assetMap[$trimmed])) {
                    $value = $assetMap[$trimmed];
                }
            }

            $prepared[$key] = $value;
        }

        if (!empty($definition['force_world_id'])) {
            $prepared['world_id'] = $worldId;
        }

        $prepared['created_at'] = $prepared['created_at'] ?? now();
        $prepared['updated_at'] = $prepared['updated_at'] ?? now();

        return $prepared;
    }

    private function resolveImportForeignKeys(array $row, array $refMap): array
    {
        $updates = [];
        $foreignMap = $this->importForeignRefMap();

        foreach ($row as $key => $value) {
            if (!str_ends_with($key, '_ref')) {
                continue;
            }

            $targetCollection = $foreignMap[$key] ?? null;
            if (!$targetCollection || !is_string($value) || $value === '') {
                continue;
            }

            $targetId = $refMap[$targetCollection][$value] ?? null;
            if (!$targetId) {
                continue;
            }

            $column = substr($key, 0, -4) . '_id';
            $updates[$column] = (int) $targetId;
        }

        return $updates;
    }

    private function importAssetsFromArchive(array $payload, ?string $assetsDir, int $worldId): array
    {
        if (!$assetsDir || !File::isDirectory($assetsDir)) {
            return [];
        }

        $map = [];
        $assets = is_array($payload['assets'] ?? null) ? $payload['assets'] : [];

        foreach ($assets as $asset) {
            if (!is_array($asset)) {
                continue;
            }

            $relative = trim((string) ($asset['path'] ?? ''));
            if ($relative === '') {
                continue;
            }

            $source = $assetsDir . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
            if (!File::exists($source) || !is_file($source)) {
                continue;
            }

            $extension = pathinfo($relative, PATHINFO_EXTENSION);
            $baseName = pathinfo($relative, PATHINFO_FILENAME);
            $safeName = Str::slug($baseName);
            if ($safeName === '') {
                $safeName = 'asset';
            }

            $destName = $safeName . '-' . Str::random(8) . ($extension !== '' ? '.' . $extension : '');
            $destPath = 'imports/world-' . $worldId . '/' . $destName;

            Storage::disk('public')->put($destPath, File::get($source));
            $map[$relative] = $destPath;
        }

        return $map;
    }

    private function uniqueImportWorldSlug(string $name): string
    {
        $base = Str::slug($name);
        $prefix = $base !== '' ? $base : 'world';
        $slug = $prefix;
        $i = 2;

        while (World::query()->where('slug', $slug)->exists()) {
            $slug = $prefix . '-' . $i;
            $i++;
        }

        return $slug;
    }

    private function importDatasetDefinitions(): array
    {
        return [
            ['public_key' => 'characters', 'table' => 'characters', 'force_world_id' => true],
            ['public_key' => 'places', 'table' => 'places', 'force_world_id' => true],
            ['public_key' => 'chronicles', 'table' => 'chronicles', 'force_world_id' => true],
            ['public_key' => 'chapters', 'table' => 'chapters', 'force_world_id' => true],
            ['public_key' => 'factions', 'table' => 'factions', 'force_world_id' => true],
            ['public_key' => 'species', 'table' => 'species', 'force_world_id' => true],
            ['public_key' => 'loreEntries', 'table' => 'lore_entries', 'force_world_id' => true],
            ['public_key' => 'jobs', 'table' => 'jobs', 'force_world_id' => true],
            ['public_key' => 'characterItems', 'table' => 'character_items', 'force_world_id' => false],
            ['public_key' => 'characterEvents', 'table' => 'character_events', 'force_world_id' => false],
            ['public_key' => 'characterGallery', 'table' => 'character_gallery_images', 'force_world_id' => false],
            ['public_key' => 'characterPowers', 'table' => 'character_powers', 'force_world_id' => false],
            ['public_key' => 'characterOutfitImages', 'table' => 'character_outfit_images', 'force_world_id' => false],
            ['public_key' => 'characterJobs', 'table' => 'character_jobs', 'force_world_id' => false],
            ['public_key' => 'characterEducations', 'table' => 'character_educations', 'force_world_id' => false],
            ['public_key' => 'diplomas', 'table' => 'diplomas', 'force_world_id' => false],
            ['public_key' => 'placeGallery', 'table' => 'place_gallery_images', 'force_world_id' => false],
            ['public_key' => 'characterRelations', 'table' => 'character_relations', 'force_world_id' => false],
            ['public_key' => 'characterExes', 'table' => 'character_exes', 'force_world_id' => false],
            ['public_key' => 'chronicleCharacters', 'table' => 'chronicle_character', 'force_world_id' => false],
            ['public_key' => 'chapterCharacters', 'table' => 'chapter_character', 'force_world_id' => false],
            ['public_key' => 'chapterPlaces', 'table' => 'chapter_place', 'force_world_id' => false],
            ['public_key' => 'chapterChronicles', 'table' => 'chapter_chronicle', 'force_world_id' => false],
            ['public_key' => 'factionMemberships', 'table' => 'faction_memberships', 'force_world_id' => false],
            ['public_key' => 'factionRelations', 'table' => 'faction_relations', 'force_world_id' => false],
            ['public_key' => 'characterSpecies', 'table' => 'character_species', 'force_world_id' => false],
        ];
    }

    private function importForeignRefMap(): array
    {
        return [
            'world_ref' => 'worlds',
            'character_ref' => 'characters',
            'from_character_ref' => 'characters',
            'to_character_ref' => 'characters',
            'ex_character_ref' => 'characters',
            'father_ref' => 'characters',
            'mother_ref' => 'characters',
            'spouse_ref' => 'characters',
            'leader_ref' => 'characters',
            'co_leader_ref' => 'characters',
            'founder_ref' => 'characters',
            'place_ref' => 'places',
            'birth_place_ref' => 'places',
            'residence_place_ref' => 'places',
            'event_place_ref' => 'places',
            'chronicle_ref' => 'chronicles',
            'chapter_ref' => 'chapters',
            'faction_ref' => 'factions',
            'related_faction_ref' => 'factions',
            'species_ref' => 'species',
            'job_ref' => 'jobs',
            'diploma_ref' => 'diplomas',
        ];
    }

    private function buildPublicExportCollections(array $datasets, array $references): array
    {
        $definitions = $this->exportDatasetDefinitions();
        $collections = [];

        foreach ($definitions as $datasetKey => $definition) {
            if (!array_key_exists($datasetKey, $datasets)) {
                continue;
            }

            $collections[$definition['public_key']] = collect($datasets[$datasetKey])
                ->map(fn (array $record) => $this->transformExportRecord($record, $references, $datasetKey))
                ->values()
                ->all();
        }

        return $collections;
    }

    private function buildExportReferences(array $datasets): array
    {
        $definitions = $this->exportDatasetDefinitions();
        $references = [];

        foreach ($datasets as $datasetKey => $rows) {
            $definition = $definitions[$datasetKey] ?? null;
            if (!$definition) {
                continue;
            }

            foreach (array_values($rows) as $index => $row) {
                $id = $row['id'] ?? null;
                if (!is_numeric($id)) {
                    continue;
                }

                $references[$datasetKey][(int) $id] = sprintf('%s_%04d', $definition['prefix'], $index + 1);
            }
        }

        return $references;
    }

    private function transformExportRecord(array $record, array $references, string $datasetKey): array
    {
        $transformed = [];

        if (isset($record['id']) && is_numeric($record['id'])) {
            $transformed['ref'] = $this->resolveExportReference($references, $datasetKey, (int) $record['id']);
        }

        foreach ($record as $key => $value) {
            if ($key === 'id') {
                continue;
            }

            if (str_ends_with($key, '_id')) {
                $datasetMap = $this->exportForeignDatasetMap();
                if (!array_key_exists($key, $datasetMap)) {
                    $transformed[$key] = $value;
                    continue;
                }

                $refKey = substr($key, 0, -3) . '_ref';
                $resolved = $this->resolveForeignExportReference($key, $value, $references);
                $transformed[$refKey] = $resolved;
                continue;
            }

            $transformed[$key] = $value;
        }

        return $transformed;
    }

    private function resolveForeignExportReference(string $key, $value, array $references)
    {
        if (!is_numeric($value)) {
            return $value;
        }

        $datasetKey = $this->exportForeignDatasetMap()[$key] ?? null;
        if (!$datasetKey) {
            return $value;
        }

        return $this->resolveExportReference($references, $datasetKey, (int) $value) ?? $value;
    }

    private function resolveExportReference(array $references, string $datasetKey, ?int $id): ?string
    {
        if ($id === null) {
            return null;
        }

        return $references[$datasetKey][$id] ?? null;
    }

    private function exportDatasetDefinitions(): array
    {
        return [
            'worlds' => ['public_key' => 'worlds', 'prefix' => 'world'],
            'characters' => ['public_key' => 'characters', 'prefix' => 'char'],
            'character_events' => ['public_key' => 'characterEvents', 'prefix' => 'cevt'],
            'character_gallery_images' => ['public_key' => 'characterGallery', 'prefix' => 'cimg'],
            'character_powers' => ['public_key' => 'characterPowers', 'prefix' => 'cpow'],
            'character_outfit_images' => ['public_key' => 'characterOutfitImages', 'prefix' => 'coutf'],
            'character_items' => ['public_key' => 'characterItems', 'prefix' => 'citem'],
            'character_jobs' => ['public_key' => 'characterJobs', 'prefix' => 'cjob'],
            'character_educations' => ['public_key' => 'characterEducations', 'prefix' => 'cedu'],
            'character_relations' => ['public_key' => 'characterRelations', 'prefix' => 'crel'],
            'character_exes' => ['public_key' => 'characterExes', 'prefix' => 'cex'],
            'places' => ['public_key' => 'places', 'prefix' => 'place'],
            'place_gallery_images' => ['public_key' => 'placeGallery', 'prefix' => 'pimg'],
            'chronicles' => ['public_key' => 'chronicles', 'prefix' => 'chron'],
            'chapters' => ['public_key' => 'chapters', 'prefix' => 'chap'],
            'chronicle_character' => ['public_key' => 'chronicleCharacters', 'prefix' => 'cchr'],
            'chapter_character' => ['public_key' => 'chapterCharacters', 'prefix' => 'chrc'],
            'chapter_place' => ['public_key' => 'chapterPlaces', 'prefix' => 'chpl'],
            'chapter_chronicle' => ['public_key' => 'chapterChronicles', 'prefix' => 'chro'],
            'factions' => ['public_key' => 'factions', 'prefix' => 'fact'],
            'faction_memberships' => ['public_key' => 'factionMemberships', 'prefix' => 'fmem'],
            'faction_relations' => ['public_key' => 'factionRelations', 'prefix' => 'frel'],
            'diplomas' => ['public_key' => 'diplomas', 'prefix' => 'dipl'],
            'lore_entries' => ['public_key' => 'loreEntries', 'prefix' => 'lore'],
            'species' => ['public_key' => 'species', 'prefix' => 'spec'],
            'character_species' => ['public_key' => 'characterSpecies', 'prefix' => 'cspec'],
            'jobs' => ['public_key' => 'jobs', 'prefix' => 'job'],
        ];
    }

    private function exportForeignDatasetMap(): array
    {
        return [
            'world_id' => 'worlds',
            'character_id' => 'characters',
            'from_character_id' => 'characters',
            'to_character_id' => 'characters',
            'ex_character_id' => 'characters',
            'father_id' => 'characters',
            'mother_id' => 'characters',
            'spouse_id' => 'characters',
            'leader_id' => 'characters',
            'co_leader_id' => 'characters',
            'founder_id' => 'characters',
            'place_id' => 'places',
            'birth_place_id' => 'places',
            'residence_place_id' => 'places',
            'chronicle_id' => 'chronicles',
            'chapter_id' => 'chapters',
            'faction_id' => 'factions',
            'related_faction_id' => 'factions',
            'species_id' => 'species',
            'job_id' => 'jobs',
        ];
    }
}




