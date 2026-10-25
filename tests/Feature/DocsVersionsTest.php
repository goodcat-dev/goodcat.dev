<?php

namespace Tests\Feature;

use Tests\TestCase;

class DocsVersionsTest extends TestCase
{
    public function test_switcher_links_to_the_same_page_in_other_versions(): void
    {
        $this->get('/docs/laravel-eloquent-querystring/2.x/filter-methods')
            ->assertOk()
            ->assertSee('aria-label="Version: 2.x"', false)
            ->assertSee('href="'.route('docs.show', ['laravel-eloquent-querystring', '1.x', 'filter-methods']).'"', false)
            ->assertDontSee('You’re reading the documentation for version');
    }

    public function test_switcher_falls_back_to_the_introduction_when_the_page_is_missing(): void
    {
        $this->get('/docs/laravel-eloquent-querystring/2.x/query-requests')
            ->assertOk()
            ->assertSee('href="'.route('docs.show', ['laravel-eloquent-querystring', '1.x', 'introduction']).'"', false);
    }

    public function test_older_versions_link_to_the_latest_one(): void
    {
        $this->get('/docs/laravel-eloquent-querystring/1.x/caching')
            ->assertOk()
            ->assertSee('You’re reading the documentation for version 1.x.')
            ->assertSee('href="'.route('docs.show', ['laravel-eloquent-querystring', '2.x', 'caching']).'"', false);
    }

    public function test_italian_switcher_keeps_the_locale(): void
    {
        $this->get('/it/docs/laravel-eloquent-querystring/1.x/caching')
            ->assertOk()
            ->assertSee('Stai consultando la documentazione della versione 1.x.')
            ->assertSee('href="'.url('/it/docs/laravel-eloquent-querystring/2.x/caching').'"', false);
    }

    public function test_switcher_is_hidden_for_packages_with_a_single_version(): void
    {
        $this->get('/docs/laravel-l10n/0.x/introduction')
            ->assertOk()
            ->assertDontSee('aria-label="Version:', false);
    }
}
