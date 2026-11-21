<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureSessionActivity;
use App\Http\Middleware\EnsureTemporaryLoginToken;
use App\Models\Character;
use App\Models\CharacterGalleryImage;
use App\Models\User;
use App\Models\World;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CharacterGalleryTest extends TestCase
{
    use RefreshDatabase;

    private function setupWorld(): array
    {
        $user = User::factory()->create();
        $world = World::create(['user_id' => $user->id, 'name' => 'Brume', 'slug' => 'brume-' . $user->id]);
        $user->forceFill(['current_world_id' => $world->id])->save();
        $this->withoutMiddleware([EnsureTemporaryLoginToken::class, EnsureSessionActivity::class])
            ->actingAs($user)->withSession(['selected_world_id' => $world->id]);
        return [$user, $world];
    }

    private function image(Character $owner, string $title = 'Photo'): CharacterGalleryImage
    {
        return CharacterGalleryImage::create([
            'world_id' => $owner->world_id, 'character_id' => $owner->id,
            'image_path' => 'test/photo.webp', 'display_path' => 'test/photo.webp',
            'title' => $title, 'description' => 'Description', 'caption' => $title,
        ]);
    }

    public function test_mentions_add_and_remove_image_in_character_room_with_eager_loaded_relations(): void
    {
        [$owner, $world] = $this->setupWorld();
        $photographed = Character::create(['world_id' => $world->id, 'name' => 'Mina']);
        $mentioned = Character::create(['world_id' => $world->id, 'name' => 'Noé']);
        $image = $this->image($photographed);

        $this->postJson(route('manage.character-gallery.mention', [$photographed, $image]), ['character_id' => $mentioned->id])->assertOk();
        $this->assertTrue($mentioned->galleryImages()->whereKey($image->id)->exists());
        $this->get(route('manage.character-gallery.show', $mentioned))->assertOk()->assertSee('Photo');

        $this->deleteJson(route('manage.character-gallery.unmention', [$mentioned, $image, $mentioned]))->assertOk();
        $this->assertFalse($mentioned->galleryImages()->whereKey($image->id)->exists());
        $this->assertDatabaseHas('character_gallery_images', ['id' => $image->id, 'deleted_at' => null]);
    }

    public function test_owner_can_upload_and_bad_file_and_invalid_mentions_are_rejected(): void
    {
        Storage::fake('public');
        [, $world] = $this->setupWorld();
        $character = Character::create(['world_id' => $world->id, 'name' => 'Mina']);
        $other = Character::create(['world_id' => $world->id, 'name' => 'Noé']);
        $response = $this->postJson(route('manage.character-gallery.store', $character), [
            'title' => 'Portrait', 'description' => '<b>Texte</b>', 'image' => UploadedFile::fake()->image('portrait.jpg', 40, 60),
            'mentions' => [$other->id],
        ]);
        $response->assertCreated();
        $this->assertDatabaseHas('character_gallery_images', ['character_id' => $character->id, 'title' => 'Portrait', 'description' => 'Texte']);
        $image = CharacterGalleryImage::latest('id')->firstOrFail();
        $this->assertDatabaseHas('gallery_image_mentions', ['gallery_image_id' => $image->id, 'character_id' => $other->id]);

        $this->postJson(route('manage.character-gallery.store', $character), [
            'title' => 'Invalide', 'image' => UploadedFile::fake()->create('faux.jpg', 10, 'text/plain'),
        ])->assertUnprocessable();
        $this->postJson(route('manage.character-gallery.store', $character), [
            'title' => 'Auto-mention', 'image' => UploadedFile::fake()->image('auto.png'), 'mentions' => [$character->id],
        ])->assertUnprocessable();
    }

    public function test_mentions_are_limited_to_same_world_and_deleting_image_clears_mentions(): void
    {
        [, $world] = $this->setupWorld();
        $ownerCharacter = Character::create(['world_id' => $world->id, 'name' => 'Mina']);
        $viewer = Character::create(['world_id' => $world->id, 'name' => 'Noé']);
        $image = $this->image($ownerCharacter);
        $otherUser = User::factory()->create();
        $otherWorld = World::create(['user_id' => $otherUser->id, 'name' => 'Autre', 'slug' => 'autre-' . $otherUser->id]);
        $foreignCharacter = Character::create(['world_id' => $otherWorld->id, 'name' => 'Iris']);

        $image->mentions()->attach($viewer->id);
        $this->postJson(route('manage.character-gallery.mention', [$viewer, $image]), ['character_id' => $foreignCharacter->id])->assertUnprocessable();
        $this->deleteJson(route('manage.character-gallery.destroy', [$viewer, $image]))->assertOk();
        $this->assertSoftDeleted('character_gallery_images', ['id' => $image->id]);
        $this->assertDatabaseMissing('gallery_image_mentions', ['gallery_image_id' => $image->id]);
    }

    public function test_deleting_a_character_removes_owned_images_and_only_its_mentions(): void
    {
        [, $world] = $this->setupWorld();
        $ownerCharacter = Character::create(['world_id' => $world->id, 'name' => 'Mina']);
        $mentioned = Character::create(['world_id' => $world->id, 'name' => 'Noé']);
        $ownedImage = $this->image($ownerCharacter, 'Photo possédée');
        $otherImage = $this->image($mentioned, 'Photo mentionnante');
        $otherImage->mentions()->attach($ownerCharacter->id);
        $ownerCharacter->delete();

        $this->assertSoftDeleted('character_gallery_images', ['id' => $ownedImage->id]);
        $this->assertDatabaseMissing('gallery_image_mentions', ['gallery_image_id' => $otherImage->id, 'character_id' => $ownerCharacter->id]);
        $this->assertDatabaseHas('character_gallery_images', ['id' => $otherImage->id, 'deleted_at' => null]);
    }

    public function test_another_world_user_cannot_open_or_mutate_the_gallery(): void
    {
        [, $world] = $this->setupWorld();
        $character = Character::create(['world_id' => $world->id, 'name' => 'Mina']);
        $other = User::factory()->create();
        $otherWorld = World::create(['user_id' => $other->id, 'name' => 'Autre', 'slug' => 'autre-' . $other->id]);
        $other->forceFill(['current_world_id' => $otherWorld->id])->save();
        $this->actingAs($other)->withSession(['selected_world_id' => $otherWorld->id]);

        $this->get(route('manage.character-gallery.show', $character))->assertForbidden();
        $this->postJson(route('manage.character-gallery.store', $character), [])->assertForbidden();
    }

    public function test_gallery_query_uses_a_fixed_number_of_queries_as_images_grow(): void
    {
        [, $world] = $this->setupWorld();
        $character = Character::create(['world_id' => $world->id, 'name' => 'Mina']);
        foreach (range(1, 3) as $number) $this->image($character, 'Photo ' . $number);
        DB::flushQueryLog(); DB::enableQueryLog();
        $character->galleryImages()->with(['owner', 'mentions'])->paginate(24)->items();
        $three = count(DB::getQueryLog());
        DB::disableQueryLog();
        foreach (range(4, 12) as $number) $this->image($character, 'Photo ' . $number);
        DB::flushQueryLog(); DB::enableQueryLog();
        $character->galleryImages()->with(['owner', 'mentions'])->paginate(24)->items();
        $twelve = count(DB::getQueryLog());
        DB::disableQueryLog();
        $this->assertSame($three, $twelve);
    }
}
