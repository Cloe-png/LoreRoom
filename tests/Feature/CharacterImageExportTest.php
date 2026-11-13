<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureTemporaryLoginToken;
use App\Models\Character;
use App\Models\User;
use App\Models\World;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CharacterImageExportTest extends TestCase
{
    use RefreshDatabase;

    private function createExportCharacter(?string $imagePath = null): Character
    {
        $user = User::factory()->create();
        $world = World::create(['user_id' => $user->id, 'name' => 'Brume', 'slug' => 'brume-' . $user->id]);
        $user->forceFill(['current_world_id' => $world->id])->save();
        $this->withoutMiddleware(EnsureTemporaryLoginToken::class)
            ->actingAs($user)
            ->withSession(['selected_world_id' => $world->id]);

        return Character::create([
            'world_id' => $world->id,
            'name' => 'Éloïse Martin',
            'image_path' => $imagePath,
        ]);
    }

    public function test_image_export_view_renders_the_same_paper_profile_without_a_portrait(): void
    {
        $character = $this->createExportCharacter();

        $this->get(route('manage.characters.export-image-view', $character))
            ->assertOk()
            ->assertSee('data-image-export-canvas', false)
            ->assertSee('#efe2c9', false)
            ->assertSee('Aucune photo')
            ->assertSee('image-export-silhouette', false);
    }

    public function test_image_export_view_uses_the_same_origin_media_url_when_a_portrait_exists(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('characters/eloise.png', 'test image');
        $character = $this->createExportCharacter('characters/eloise.png');

        $this->get(route('manage.characters.export-image-view', $character))
            ->assertOk()
            ->assertSee(route('media.show', ['path' => 'characters/eloise.png']), false)
            ->assertSee('data-image-export-canvas', false);
    }
}
