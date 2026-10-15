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
            ->assertSee('href="'.route('docs.index', 'laravel-l10n').'"', false)
            ->assertSee('href="https://github.com/goodcat-dev/laravel-filter-querystring"', false)
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

    public function test_docs_introduction_renders_hero_guides_and_features(): void
    {
        $this->get('/docs/laravel-l10n/1.x/introduction')
            ->assertOk()
            ->assertSee('composer require goodcat/laravel-l10n')
            ->assertSee('<h2 id="at-a-glance">', false)
            ->assertSee('role="tablist"', false)
            ->assertSee('Read the user’s preferred locales')
            ->assertSee(route('docs.show', ['laravel-l10n', '1.x', 'quickstart']))
            ->assertSee('--pkg-main: var(--color-mint)', false);
    }

    public function test_every_page_in_the_navigation_renders(): void
    {
        foreach (['quickstart', 'localized-routes', 'url-generation', 'locale-preference', 'helpers', 'components', 'localized-views', 'javascript'] as $page) {
            $this->get("/docs/laravel-l10n/1.x/{$page}")->assertOk()->assertSee('class="phiki', false);
        }
    }

    public function test_docs_page_renders_alerts_tables_and_section_links(): void
    {
        $this->get('/docs/laravel-l10n/1.x/localized-routes')
            ->assertOk()
            ->assertSeeInOrder(['// routing', 'Localized Routes'])
            ->assertSee('// warning')
            ->assertSee('class="table-wrap"', false)
            ->assertSee('href="#route-strategy"', false)
            ->assertSee('<h2 id="route-strategy">', false)
            ->assertSee(route('docs.show', ['laravel-l10n', '1.x', 'url-generation']));
    }

    public function test_unknown_docs_return_not_found(): void
    {
        $this->get('/docs/missing-package')->assertNotFound();
        $this->get('/docs/missing-package/1.x/introduction')->assertNotFound();
        $this->get('/docs/laravel-l10n/1.x/missing-page')->assertNotFound();
        $this->get('/docs/laravel-l10n/2.x/introduction')->assertNotFound();
    }
}
