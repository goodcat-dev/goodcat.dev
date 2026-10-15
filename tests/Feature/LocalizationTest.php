<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocalizationTest extends TestCase
{
    public function test_italian_pages_are_served_under_the_it_prefix(): void
    {
        $this->get('/it')
            ->assertOk()
            ->assertSee('<html lang="it">', false)
            ->assertSee('href="'.url('/it/blog/localized-routes-in-vue').'"', false);
    }

    public function test_english_pages_stay_unprefixed(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSee('href="'.url('/blog/localized-routes-in-vue').'"', false);
    }

    public function test_italian_pages_link_to_english_docs_until_they_are_translated(): void
    {
        $this->get('/it')
            ->assertSee('href="'.url('/docs/laravel-l10n').'"', false)
            ->assertDontSee('href="'.url('/it/docs/laravel-l10n').'"', false);

        $this->get('/it/blog/localized-routes-in-vue')
            ->assertOk()
            ->assertSee('href="'.url('/docs/laravel-l10n').'"', false);
    }

    public function test_italian_docs_redirect_within_the_italian_site(): void
    {
        $this->get('/it/docs/laravel-l10n')
            ->assertRedirect(url('/it/docs/laravel-l10n/0.x/introduction'));
    }

    public function test_docs_pages_without_an_italian_translation_are_not_found(): void
    {
        $this->get('/it/docs/laravel-l10n/0.x/quickstart')->assertNotFound();
    }

    public function test_switcher_links_the_current_page_in_every_language(): void
    {
        $this->get('/blog/localized-routes-in-vue')
            ->assertSee('href="'.url('/it/blog/localized-routes-in-vue').'" hreflang="it"', false)
            ->assertSee('<link rel="alternate" hreflang="it" href="'.url('/it/blog/localized-routes-in-vue').'">', false)
            ->assertSee('<link rel="alternate" hreflang="x-default" href="'.url('/blog/localized-routes-in-vue').'">', false);

        $this->get('/it')
            ->assertSee('href="'.url('/').'" hreflang="en"', false);
    }

    public function test_switcher_disables_languages_without_a_translation(): void
    {
        $this->get('/docs/laravel-l10n/0.x/quickstart')
            ->assertOk()
            ->assertSee('not translated yet')
            ->assertDontSee('href="'.url('/it/docs/laravel-l10n/0.x/quickstart').'"', false)
            ->assertDontSee('rel="alternate"', false);
    }

    public function test_old_major_urls_are_not_found(): void
    {
        $this->get('/docs/laravel-l10n/1.x/introduction')->assertNotFound();
    }
}
