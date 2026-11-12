<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureTemporaryLoginToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class TourTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(EnsureTemporaryLoginToken::class);
    }

    public function test_registration_creates_an_unseen_tour_state(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Nouveau membre',
            'email' => 'new-tour@example.test',
            'locale' => 'fr',
            'roles' => ['scenariste'],
            'password' => 'TourTestPassword1!',
            'password_confirmation' => 'TourTestPassword1!',
        ])->assertRedirect(route('manage.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'new-tour@example.test',
            'tour_completed_at' => null,
        ]);
    }

    public function test_complete_and_restart_change_only_the_authenticated_users_state(): void
    {
        $user = User::factory()->create(['tour_completed_at' => null]);
        $other = User::factory()->create(['tour_completed_at' => now()->subDay()]);

        $this->actingAs($user)
            ->postJson(route('manage.tour.complete'))
            ->assertOk()
            ->assertJsonPath('completed', true);
        $this->assertNotNull($user->fresh()->tour_completed_at);
        $this->assertEquals($other->tour_completed_at->timestamp, $other->fresh()->tour_completed_at->timestamp);

        $this->postJson(route('manage.tour.restart'))
            ->assertOk()
            ->assertJsonPath('completed', false);
        $this->assertNull($user->fresh()->tour_completed_at);
        $this->assertEquals($other->tour_completed_at->timestamp, $other->fresh()->tour_completed_at->timestamp);
    }

    public function test_non_admin_html_has_no_admin_tour_steps_or_targets(): void
    {
        $user = User::factory()->create(['role' => 'utilisateur', 'roles' => []]);

        $response = $this->actingAs($user)->get(route('manage.index'))->assertOk();
        $response->assertDontSee('data-tour="analytics"', false)
            ->assertDontSee('data-tour="accounts"', false)
            ->assertDontSee(__('tour.steps.analytics.text'))
            ->assertDontSee(__('tour.steps.accounts.text'));
    }

    public function test_admin_and_chef_receive_the_privileged_tour_targets(): void
    {
        foreach (['admin', 'chef'] as $role) {
            $user = User::factory()->create(['role' => $role, 'roles' => []]);
            $response = $this->actingAs($user)->get(route('manage.index'))->assertOk();
            $response->assertSee('data-tour="analytics"', false)
                ->assertSee('data-tour="accounts"', false)
                ->assertSee(__('tour.steps.analytics.text'))
                ->assertSee(__('tour.steps.accounts.text'));
        }
    }

    public function test_user_without_world_sees_create_world_as_the_last_action(): void
    {
        $user = User::factory()->create(['tour_completed_at' => null, 'preferred_locale' => 'fr']);
        $createRoute = app('router')->getRoutes()->getByName('manage.worlds.create');
        $this->assertNotContains('world.selected', $createRoute->gatherMiddleware());

        $response = $this->actingAs($user)->get(route('manage.index'))->assertOk();
        $steps = $this->readTourSteps($response->getContent());
        $finish = end($steps);

        $this->assertSame('create-world', $finish['action']);
        $this->assertSame(__('tour.actions.create_world'), $finish['actionLabel']);
        $this->assertSame('Créer mon monde', $finish['actionLabel']);
        $this->assertSame(__('tour.actions.later'), $finish['skipLabel']);
        $this->assertSame(__('tour.finish_create'), $finish['text']);
        $this->assertSame('Voilà, tu connais chaque coin du carnet. Il ne te manque qu\'un monde pour commencer. On le crée maintenant ?', $finish['text']);

        $completion = $this->postJson(route('manage.tour.complete'), ['create_world' => true])
            ->assertOk()
            ->assertJsonPath('completed', true)
            ->assertJsonPath('redirect', route('manage.worlds.create'))
            ->assertSessionHas('success', __('tour.create_world_hint'));

        $this->assertNotNull($user->fresh()->tour_completed_at);
        $this->get(route('manage.worlds.create'))
            ->assertOk()
            ->assertSee('id="world-name"', false)
            ->assertSee('autofocus', false)
            ->assertSee(__('tour.create_world_hint'));
    }

    public function test_user_with_an_active_world_gets_the_regular_finish_action(): void
    {
        $user = User::factory()->create(['tour_completed_at' => null]);
        $world = $user->worlds()->create([
            'name' => 'Monde de test',
            'slug' => 'monde-de-test',
            'status' => 'active',
        ]);
        $user->forceFill(['current_world_id' => $world->id])->save();

        $response = $this->actingAs($user)->get(route('manage.index'))->assertOk();
        $steps = $this->readTourSteps($response->getContent());
        $finish = end($steps);

        $this->assertSame('complete', $finish['action']);
        $this->assertSame(__('tour.actions.finish'), $finish['actionLabel']);
        $this->assertSame(__('tour.finish_active'), $finish['text']);

        $completion = $this->postJson(route('manage.tour.complete'))
            ->assertOk()
            ->assertJsonPath('completed', true);
        $this->assertArrayNotHasKey('redirect', $completion->json());
    }

    public function test_restart_button_is_in_the_sidebar_and_missing_image_uses_question_mark(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('manage.index'))
            ->assertOk()
            ->assertSee('class="rv"', false)
            ->assertSee('aria-label="' . __('tour.actions.restart') . '"', false)
            ->assertSee('data-tour="review"', false)
            ->assertSee('role="tooltip"', false);

        $this->get(route('manage.worlds.create'))
            ->assertOk()
            ->assertSee('class="rv"', false);

        $asset = public_path('images/mascot/help.jpg');
        $backup = $asset . '.test-backup';
        $this->assertFileExists($asset);
        try {
            $this->assertTrue(rename($asset, $backup));
            $this->get(route('manage.index'))
                ->assertOk()
                ->assertSee('<span class="rv-fallback" aria-hidden="true">?</span>', false)
                ->assertDontSee('images/mascot/help.jpg', false);
        } finally {
            if (file_exists($backup)) rename($backup, $asset);
        }

        $this->postJson(route('manage.tour.restart'))
            ->assertOk()
            ->assertJsonPath('completed', false);
        $this->assertNull($user->fresh()->tour_completed_at);
    }

    public function test_tour_does_not_require_an_active_world_and_handles_a_missing_mascot_file(): void
    {
        $user = User::factory()->create(['tour_completed_at' => null]);
        $asset = public_path('images/mascot/tour-parle-2.webp');
        $backup = $asset . '.test-backup';
        $this->assertFileExists($asset);

        try {
            $this->assertTrue(rename($asset, $backup));
            Cache::forget('tour-mascot.assets-present');

            $response = $this->actingAs($user)->get(route('manage.index'))->assertOk();
            $response->assertSee('id="first-visit-tour"', false)
                ->assertDontSee('<div class="tour-mascot"', false);
        } finally {
            if (file_exists($backup)) rename($backup, $asset);
            Cache::forget('tour-mascot.assets-present');
        }
    }

    public function test_tour_routes_are_throttled_after_thirty_requests_per_minute(): void
    {
        $this->actingAs(User::factory()->create());

        for ($attempt = 0; $attempt < 30; $attempt++) {
            $this->postJson(route('manage.tour.complete'))->assertOk();
        }

        $this->postJson(route('manage.tour.complete'))->assertStatus(429);
    }

    private function readTourSteps(string $html): array
    {
        $this->assertSame(1, preg_match('/<script type="application\/json" data-tour-steps>(.*?)<\/script>/s', $html, $matches));
        $steps = json_decode($matches[1], true);
        $this->assertIsArray($steps);

        return $steps;
    }
}
