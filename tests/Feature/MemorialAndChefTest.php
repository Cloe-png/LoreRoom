<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureTemporaryLoginToken;
use App\Models\Character;
use App\Models\MemorialOffering;
use App\Models\User;
use App\Models\WritingPrompt;
use App\Models\World;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MemorialAndChefTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(EnsureTemporaryLoginToken::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_secret_button_is_rendered_only_on_december_21_or_when_forced(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        Carbon::setTestNow(Carbon::parse('2026-11-08 10:00:00', 'UTC'));
        config(['memorial.force_visible' => false]);
        $this->get(route('manage.index'))->assertOk()->assertDontSee('aria-label="Hayate"', false);

        Carbon::setTestNow(Carbon::parse('2026-12-21 10:00:00', 'UTC'));
        $this->get(route('manage.index'))->assertOk()->assertSee('aria-label="Hayate"', false);

        Carbon::setTestNow(Carbon::parse('2026-11-08 10:00:00', 'UTC'));
        config(['memorial.force_visible' => true]);
        $this->get(route('manage.index'))->assertOk()->assertSee('aria-label="Hayate"', false);
        Carbon::setTestNow();
    }

    public function test_chef_bypasses_gates_and_admin_cannot_change_or_delete_chef(): void
    {
        Gate::define('feature-denied', fn () => false);
        $chef = User::factory()->create(['role' => 'chef']);
        $this->assertTrue(Gate::forUser($chef)->allows('feature-denied'));

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $ordinary = User::factory()->create(['role' => 'utilisateur']);
        $this->put(route('manage.users.update', $ordinary), ['role' => 'chef'])->assertSessionHasErrors('role');
        $this->put(route('manage.users.update', $chef), ['role' => 'utilisateur'])->assertForbidden();
        $this->delete(route('manage.users.destroy', $chef))->assertForbidden();
    }

    public function test_only_chef_can_read_anonymous_stats_and_non_chef_page_has_no_chef_block(): void
    {
        $user = User::factory()->create(['role' => 'utilisateur']);
        $this->actingAs($user);
        $this->get(route('manage.memorial.stats'))->assertNotFound();
        $this->get(route('manage.memorial.show'))->assertOk()->assertDontSee('Vue chef');

        $chef = User::factory()->create(['role' => 'chef']);
        $this->actingAs($chef)->get(route('manage.memorial.stats'))->assertOk()->assertJsonStructure(['people','total','today','last_seven_days']);
    }

    public function test_flower_offering_is_idempotent_and_returns_success(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->postJson(route('manage.memorial.offer'))->assertOk()->assertJsonPath('offered', true);
        $this->postJson(route('manage.memorial.offer'))->assertOk()->assertJsonPath('message', 'Fleur déjà offerte aujourd’hui.');
        $this->assertSame(1, MemorialOffering::where('user_id', $user->id)->count());
    }

    public function test_flower_can_be_offered_again_the_next_day_and_is_deleted_with_its_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        Carbon::setTestNow(Carbon::parse('2026-12-21 12:00:00', 'UTC'));
        $this->postJson(route('manage.memorial.offer'))->assertOk();
        Carbon::setTestNow(Carbon::parse('2026-12-22 12:00:00', 'UTC'));
        $this->postJson(route('manage.memorial.offer'))->assertOk();
        $this->assertSame(2, MemorialOffering::where('user_id', $user->id)->count());
        $user->delete();
        $this->assertSame(0, MemorialOffering::where('user_id', $user->id)->count());
    }

    public function test_offering_endpoint_is_throttled_after_thirty_requests_per_minute(): void
    {
        $this->actingAs(User::factory()->create());
        for ($attempt = 0; $attempt < 30; $attempt++) {
            $this->postJson(route('manage.memorial.offer'))->assertOk();
        }
        $this->postJson(route('manage.memorial.offer'))->assertStatus(429);
    }

    public function test_chef_statistics_use_aggregate_counts_and_include_zero_days(): void
    {
        $first = User::factory()->create();
        $second = User::factory()->create();
        $chef = User::factory()->create(['role' => 'chef']);
        Carbon::setTestNow(Carbon::parse('2026-12-21 12:00:00', 'UTC'));
        MemorialOffering::create(['user_id' => $first->id, 'offered_on' => '2026-12-20']);
        MemorialOffering::create(['user_id' => $first->id, 'offered_on' => '2026-12-21']);
        MemorialOffering::create(['user_id' => $second->id, 'offered_on' => '2026-12-20']);

        $this->actingAs($chef)->getJson(route('manage.memorial.stats'))
            ->assertOk()->assertJsonPath('people', 2)->assertJsonPath('total', 3)->assertJsonPath('today', 1)
            ->assertJsonCount(7, 'last_seven_days')->assertJsonPath('last_seven_days.0.count', 0);
    }

    public function test_dashboard_shows_onboarding_only_for_accounts_not_marked_as_existing(): void
    {
        $newUser = User::factory()->create(['onboarding_completed_at' => null]);
        $this->actingAs($newUser)->get(route('manage.index'))->assertOk()->assertSee('Premiers pas')
            ->assertDontSee('Créer rapidement')->assertDontSee('Activité récente');
        $existingUser = User::factory()->create(['onboarding_completed_at' => now()->subDay()]);
        $this->actingAs($existingUser)->get(route('manage.index'))->assertOk()->assertDontSee('Premiers pas');
    }

    public function test_home_without_a_world_shows_only_onboarding_news_and_suggestions(): void
    {
        $user = User::factory()->create(['roles' => ['moderator']]);
        DB::table('changelog_entries')->insert([
            ['title' => 'Publié', 'body' => '<p>Texte visible</p>', 'is_published' => true, 'published_at' => now(), 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Brouillon secret', 'body' => 'Ne pas afficher', 'is_published' => false, 'published_at' => now(), 'created_at' => now(), 'updated_at' => now()],
        ]);
        $this->actingAs($user)->get(route('manage.index'))->assertOk()->assertSee('Premiers pas')
            ->assertSee('Publié')->assertDontSee('Brouillon secret')->assertSee('Une idée pour Loreroom ?')
            ->assertDontSee('Idée du jour')->assertDontSee('À compléter')->assertDontSee('Chronologie');
    }

    public function test_daily_prompt_is_stable_until_an_alternative_is_requested_and_changes_tomorrow(): void
    {
        $user = User::factory()->create();
        $world = $user->worlds()->create(['name' => 'Monde test', 'slug' => 'monde-test-' . $user->id]);
        $user->forceFill(['current_world_id' => $world->id])->save();
        Character::create(['world_id' => $world->id, 'name' => 'Ariane']);
        for ($index = 1; $index <= 30; $index++) {
            WritingPrompt::create(['text' => 'Question ' . $index . ' pour {nom}', 'target_type' => 'character', 'is_active' => true]);
        }
        Carbon::setTestNow(Carbon::parse('2026-11-08 10:00:00', 'UTC'));
        $this->actingAs($user);

        $first = $this->get(route('manage.index'))->assertOk()->viewData('idea')['text'];
        $this->get(route('manage.index'))->assertOk()->assertSee($first);
        $this->post(route('manage.prompts.another'))->assertOk();
        $alternative = $this->get(route('manage.index'))->assertOk()->viewData('idea')['text'];
        $this->assertNotSame($first, $alternative);
        $this->get(route('manage.index'))->assertOk()->assertSee($alternative);

        Carbon::setTestNow(Carbon::parse('2026-11-09 10:00:00', 'UTC'));
        $tomorrow = $this->get(route('manage.index'))->assertOk()->viewData('idea')['text'];
        $this->assertNotSame($alternative, $tomorrow);
    }
}
