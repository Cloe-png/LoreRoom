<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureSessionActivity;
use App\Http\Middleware\EnsureTemporaryLoginToken;
use App\Models\Character;
use App\Models\User;
use App\Models\World;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_main_pages_load_for_a_chef_with_an_active_world(): void
    {
        $chef = User::factory()->create(['role' => 'chef', 'roles' => []]);
        $world = World::create([
            'user_id' => $chef->id,
            'name' => 'Monde de test',
            'slug' => 'monde-de-test-' . $chef->id,
            'status' => 'active',
        ]);
        $chef->forceFill(['current_world_id' => $world->id])->save();
        $character = Character::create([
            'world_id' => $world->id,
            'name' => 'Personnage de test',
        ]);

        $this->withoutMiddleware([
            EnsureTemporaryLoginToken::class,
            EnsureSessionActivity::class,
        ])->actingAs($chef)->withSession(['selected_world_id' => $world->id]);

        $pages = [
            'accueil' => route('manage.index'),
            'mondes' => route('manage.worlds.index'),
            'personnages' => route('manage.characters.index'),
            'création de personnage' => route('manage.characters.create'),
            'fiche personnage' => route('manage.characters.show', $character),
            'lieux' => route('manage.places.index'),
            'chapitres' => route('manage.chapters.index'),
            'lore' => route('manage.lore.index'),
            'métiers' => route('manage.jobs.index'),
            'factions' => route('manage.factions.index'),
            'espèces' => route('manage.species.index'),
            'chronologie' => route('manage.chronicles.index'),
            'relations' => route('manage.relations.index'),
            'arbre généalogique' => route('manage.genealogy.index'),
            'galerie' => route('manage.gallery.index', ['view' => 'grid']),
            'musée' => route('manage.gallery.index'),
            'suggestions' => route('manage.suggestions.index'),
            'nouveautés' => route('manage.nouveautes.index'),
            'comptes' => route('manage.users.index'),
            'analytics' => route('manage.analytics.index'),
            'connexion' => route('login'),
            'inscription' => route('register'),
        ];

        foreach ($pages as $label => $url) {
            $response = $this->get($url);

            $this->assertContains(
                $response->getStatusCode(),
                [200, 302],
                sprintf('La page « %s » doit répondre 200 ou 302.', $label)
            );
        }
    }
}
