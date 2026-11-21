<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\World;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_requires_authentication_and_limits_worlds_to_the_owner(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $ownedWorld = World::create(['user_id' => $owner->id, 'name' => 'Mine', 'slug' => 'mine']);
        $otherWorld = World::create(['user_id' => $other->id, 'name' => 'Other', 'slug' => 'other']);

        $this->getJson('/api/worlds')->assertUnauthorized();

        Sanctum::actingAs($owner);
        $this->getJson('/api/worlds')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $ownedWorld->id);
        $this->getJson('/api/worlds/' . $otherWorld->id)->assertNotFound();
        $this->postJson('/api/characters', [
            'world_id' => $otherWorld->id,
            'name' => 'Injected',
        ])->assertNotFound();
    }

    public function test_api_world_creation_uses_the_authenticated_owner(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        Sanctum::actingAs($owner);

        $this->postJson('/api/worlds', [
            'user_id' => $other->id,
            'name' => 'Created',
            'slug' => 'created',
        ])->assertNotFound();

        $this->postJson('/api/worlds', [
            'name' => 'Created',
            'slug' => 'created',
        ])->assertCreated()->assertJsonPath('data.user_id', $owner->id);
    }
}
