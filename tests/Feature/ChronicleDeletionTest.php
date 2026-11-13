<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureTemporaryLoginToken;
use App\Models\Character;
use App\Models\Chronicle;
use App\Models\Place;
use App\Models\User;
use App\Models\World;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChronicleDeletionTest extends TestCase
{
    use RefreshDatabase;

    private function worldFor(User $user): World
    {
        return World::create(['user_id' => $user->id, 'name' => 'Monde', 'slug' => 'monde-' . $user->id]);
    }

    private function actAsWorldOwner(User $user, World $world): void
    {
        $user->forceFill(['current_world_id' => $world->id, 'login_token_hash' => hash('sha256', 'test-token'), 'login_token_expires_at' => now()->addHour()])->save();
        $this->withoutMiddleware(EnsureTemporaryLoginToken::class)
            ->actingAs($user)
            ->withSession(['selected_world_id' => $world->id, 'login_temp_token' => 'test-token']);
    }

    public function test_world_owner_can_delete_chronicle_and_its_pivots_without_deleting_linked_records(): void
    {
        $user = User::factory()->create();
        $world = $this->worldFor($user);
        $this->actAsWorldOwner($user, $world);
        $character = Character::create(['world_id' => $world->id, 'name' => 'Ada']);
        $place = Place::create(['world_id' => $world->id, 'name' => 'Port', 'type' => 'ville', 'short_description' => 'Port']);
        $chronicle = Chronicle::create(['world_id' => $world->id, 'title' => 'Départ']);
        $chronicle->linkedCharacters()->attach($character);
        $chronicle->places()->attach($place);

        $this->delete(route('manage.chronicles.destroy', $chronicle))->assertRedirect(route('manage.chronicles.index'));

        $this->assertSoftDeleted('chronicles', ['id' => $chronicle->id]);
        $this->assertDatabaseMissing('chronicle_character', ['chronicle_id' => $chronicle->id]);
        $this->assertDatabaseMissing('chronicle_place', ['chronicle_id' => $chronicle->id]);
        $this->assertDatabaseHas('characters', ['id' => $character->id]);
        $this->assertDatabaseHas('places', ['id' => $place->id]);
    }

    public function test_another_user_cannot_delete_chronicle(): void
    {
        $owner = User::factory()->create();
        $world = $this->worldFor($owner);
        $chronicle = Chronicle::create(['world_id' => $world->id, 'title' => 'Privée']);
        $other = User::factory()->create();
        $otherWorld = $this->worldFor($other);
        $this->actAsWorldOwner($other, $otherWorld);

        $this->delete(route('manage.chronicles.destroy', $chronicle))->assertForbidden();
        $this->assertDatabaseHas('chronicles', ['id' => $chronicle->id, 'deleted_at' => null]);
    }
}
