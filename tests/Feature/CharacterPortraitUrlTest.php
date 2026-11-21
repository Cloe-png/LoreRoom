<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureTemporaryLoginToken;
use App\Models\Character;
use App\Models\User;
use App\Models\World;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CharacterPortraitUrlTest extends TestCase
{
    use RefreshDatabase;

    private function signedInWorld(): array
    {
        $user = User::factory()->create();
        $world = World::create(['user_id' => $user->id, 'name' => 'Brume', 'slug' => 'brume-' . $user->id]);
        $user->forceFill(['current_world_id' => $world->id])->save();
        $this->withoutMiddleware(EnsureTemporaryLoginToken::class)
            ->actingAs($user)
            ->withSession(['selected_world_id' => $world->id]);

        return [$user, $world];
    }

    public function test_uploaded_portrait_is_saved_and_the_same_canonical_url_is_used_on_the_sheet_and_museum(): void
    {
        Storage::fake('public');
        [, $world] = $this->signedInWorld();

        $this->post(route('manage.characters.store'), [
            'first_name' => 'Éloïse',
            'last_name' => 'Martin',
            'role' => 'Personnage principal',
            'image' => UploadedFile::fake()->image('portrait.jpg'),
        ])->assertRedirect();

        $character = Character::where('world_id', $world->id)->firstOrFail();
        $this->assertStringStartsWith('characters/', $character->image_path);
        Storage::disk('public')->assertExists($character->image_path);

        $portraitUrl = $character->portrait_url;
        $this->assertSame(route('media.show', [
            'path' => $character->image_path,
            'v' => Storage::disk('public')->lastModified($character->image_path),
        ], false), $portraitUrl);
        $this->get(route('manage.characters.show', $character))
            ->assertOk()
            ->assertSee('src="' . $portraitUrl . '"', false);
        $this->get(route('manage.gallery.index'))
            ->assertOk()
            ->assertSee('src="' . $portraitUrl . '"', false)
            ->assertSee('alt="Portrait de Éloïse Martin"', false);
    }

    public function test_character_without_a_portrait_has_no_portrait_url_and_museum_shows_silhouette(): void
    {
        [, $world] = $this->signedInWorld();
        $character = Character::create([
            'world_id' => $world->id,
            'first_name' => 'Nora',
            'name' => 'Nora',
            'role' => 'Figurant',
        ]);

        $this->assertNull($character->portrait_url);
        $this->get(route('manage.gallery.index', ['without_portrait' => 1]))
            ->assertOk()
            ->assertSee('aria-hidden="false"', false)
            ->assertSee('N', false);
    }
}
