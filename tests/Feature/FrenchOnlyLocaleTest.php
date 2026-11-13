<?php

namespace Tests\Feature;

use Tests\TestCase;

class FrenchOnlyLocaleTest extends TestCase
{
    public function test_old_english_locale_query_is_ignored_and_pages_render_in_french(): void
    {
        $this->assertSame('fr', config('app.locale'));
        $this->assertSame('fr', config('app.fallback_locale'));
        $this->get('/?lang=en')
            ->assertOk()
            ->assertSee('<html', false)
            ->assertSee('lang="fr"', false)
            ->assertDontSee('Sign in')
            ->assertDontSee('English');

        $this->get(route('login') . '?lang=en')
            ->assertOk()
            ->assertSee('lang="fr"', false)
            ->assertDontSee('Sign in');
    }
}
