<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureTemporaryLoginToken;
use App\Http\Middleware\EnsureWorldSelected;
use App\Models\Character;
use App\Models\User;
use App\Models\World;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BirthdayBannerTest extends TestCase
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

    public function test_character_with_birthday_today_is_shown_on_the_homepage(): void
    {
        Carbon::setTestNow('2026-04-18 12:00:00');
        [$user, $world] = $this->userAndWorld();
        $this->character($world, 'Camille', '1990-04-18');

        $this->actingAs($user)->get(route('manage.index'))
            ->assertOk()
            ->assertSee('Jour de fête')
            ->assertSee('Camille (36 ans)')
            ->assertSee('Ouvrir la fiche')
            ->assertSee('Rejouer la fête')
            ->assertSee('Fermer pour aujourd');
    }

    public function test_character_born_on_another_day_is_not_shown(): void
    {
        Carbon::setTestNow('2026-04-18 12:00:00');
        [$user, $world] = $this->userAndWorld();
        $this->character($world, 'Camille', '1990-04-17');

        $this->actingAs($user)->get(route('manage.index'))
            ->assertOk()
            ->assertDontSee('data-birthday-banner', false)
            ->assertDontSee('Camille (36 ans)');
    }

    public function test_february_29_birthday_is_celebrated_on_february_28_in_a_non_leap_year(): void
    {
        Carbon::setTestNow('2025-02-28 12:00:00');
        [$user, $world] = $this->userAndWorld();
        $this->character($world, 'Camille', '2000-02-29');

        $this->actingAs($user)->get(route('manage.index'))
            ->assertOk()
            ->assertSee('Camille (25 ans)');
    }

    public function test_two_birthdays_appear_in_one_banner(): void
    {
        Carbon::setTestNow('2026-04-18 12:00:00');
        [$user, $world] = $this->userAndWorld();
        $this->character($world, 'Alice', '1984-04-18');
        $this->character($world, 'Benoît', '1981-04-18');

        $html = $this->actingAs($user)->get(route('manage.index'))
            ->assertOk()
            ->assertSee('Alice (42 ans) et Benoît (45 ans)')
            ->getContent();

        $this->assertSame(1, substr_count($html, 'data-birthday-banner'));
    }

    public function test_characters_from_other_worlds_and_accounts_are_excluded(): void
    {
        Carbon::setTestNow('2026-04-18 12:00:00');
        [$user, $world] = $this->userAndWorld();
        $this->character($world, 'Visible', '1990-04-18');
        [$otherUser, $otherWorld] = $this->userAndWorld('other');
        $this->character($otherWorld, 'Secret', '1990-04-18');

        $this->actingAs($user)->get(route('manage.index'))
            ->assertOk()
            ->assertSee('Visible (36 ans)')
            ->assertDontSee('Secret');
    }

    public function test_homepage_without_an_active_world_has_no_birthday_banner(): void
    {
        Carbon::setTestNow('2026-04-18 12:00:00');
        [$user, $world] = $this->userAndWorld();
        $this->character($world, 'Hidden', '1990-04-18');
        $user->forceFill(['current_world_id' => null])->save();

        $this->withoutMiddleware(EnsureWorldSelected::class)
            ->actingAs($user)
            ->withSession(['selected_world_id' => null])
            ->get(route('manage.index'))
            ->assertOk()
            ->assertDontSee('data-birthday-banner', false);
    }

    public function test_deceased_character_gets_the_sober_message_without_celebration_animation(): void
    {
        Carbon::setTestNow('2026-04-18 12:00:00');
        [$user, $world] = $this->userAndWorld();
        $this->character($world, 'Camille', '1990-04-18', ['first_name' => 'Camille', 'death_date' => '2020-02-01']);

        $this->actingAs($user)->get(route('manage.index'))
            ->assertOk()
            ->assertSee('Une pensée')
            ->assertSee("Aujourd'hui, Camille aurait eu 36 ans.")
            ->assertSee('Une bougie pour Camille')
            ->assertDontSee('data-birthday-garland', false)
            ->assertDontSee('Rejouer la fête');
    }

    public function test_missing_birth_date_is_ignored(): void
    {
        Carbon::setTestNow('2026-04-18 12:00:00');
        [$user, $world] = $this->userAndWorld();
        $this->character($world, 'Sans date', null);

        $this->actingAs($user)->get(route('manage.index'))
            ->assertOk()
            ->assertDontSee('data-birthday-banner', false);
    }

    private function userAndWorld(string $suffix = ''): array
    {
        $user = User::factory()->create();
        $world = $user->worlds()->create([
            'name' => 'Monde ' . $suffix,
            'slug' => 'monde-' . $user->id . '-' . ($suffix ?: 'principal'),
        ]);
        $user->forceFill(['current_world_id' => $world->id])->save();

        return [$user, $world];
    }

    private function character(World $world, string $name, ?string $birthDate, array $attributes = []): Character
    {
        return Character::create(array_merge([
            'world_id' => $world->id,
            'name' => $name,
            'birth_date' => $birthDate,
        ], $attributes));
    }
}
