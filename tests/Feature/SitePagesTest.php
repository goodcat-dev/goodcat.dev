<?php

namespace Tests\Feature;

use Tests\TestCase;

class SitePagesTest extends TestCase
{
    public function test_home_lists_packages_and_posts(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('laravel-l10n')
            ->assertSee('composer require goodcat/laravel-l10n')
            ->assertSee('Translated URLs without a second router')
            ->assertSee(route('posts.show', 'localized-routes-in-vue'));
    }

    public function test_post_page_renders_the_article_and_its_package(): void
    {
        $this->get(route('posts.show', 'localized-routes-in-vue'))
            ->assertOk()
            ->assertSee('Pick your stack')
            ->assertSee('this article runs on')
            ->assertSee(route('docs.index', 'laravel-l10n'))
            ->assertSee('Translated URLs without a second router');
    }

    public function test_unknown_post_returns_not_found(): void
    {
        $this->get('/blog/missing-post')->assertNotFound();
    }

    public function test_package_docs_redirect_to_the_current_version(): void
    {
        $this->get('/docs/laravel-l10n')
            ->assertRedirect(route('docs.show', ['laravel-l10n', '1.x', 'introduction']));
    }

    public function test_docs_page_renders_navigation_and_content(): void
    {
        $this->get('/docs/laravel-l10n/1.x/introduction')
            ->assertOk()
            ->assertSee('At a glance')
            ->assertSee('JavaScript URL Generation')
            ->assertSee('--pkg-main: var(--color-mint)', false);
    }

    public function test_unknown_docs_return_not_found(): void
    {
        $this->get('/docs/missing-package')->assertNotFound();
        $this->get('/docs/missing-package/1.x/introduction')->assertNotFound();
        $this->get('/docs/laravel-l10n/1.x/missing-page')->assertNotFound();
        $this->get('/docs/laravel-l10n/2.x/introduction')->assertNotFound();
    }
}
