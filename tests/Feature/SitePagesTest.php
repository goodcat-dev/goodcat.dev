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
            ->assertSee('href="'.route('docs.index', 'laravel-eloquent-querystring').'"', false)
            ->assertSee('Localized routes in Vue without passing lang every time')
            ->assertSee(route('posts.show', 'localized-routes-in-vue'))
            ->assertSee('href="'.route('posts.index').'"', false);
    }

    public function test_blog_archive_lists_every_post_by_year(): void
    {
        $this->get(route('posts.index'))
            ->assertOk()
            ->assertSee('1 post')
            ->assertSeeInOrder(['2026', 'Localized routes in Vue without passing lang every time'])
            ->assertSee(route('posts.show', 'localized-routes-in-vue'))
            ->assertSee('aria-current="page"', false);
    }

    public function test_post_page_renders_the_article_and_its_package(): void
    {
        $this->get(route('posts.show', 'localized-routes-in-vue'))
            ->assertOk()
            ->assertSee('Pick your stack')
            ->assertSee('this article runs on')
            ->assertSee(route('docs.index', 'laravel-l10n'))
            ->assertSee('font-mono text-[0.8em] font-medium tracking-[-0.04em] text-on-accent squircle">lang</span>', false)
            ->assertSee('// before you start')
            ->assertSee('<h2 id="wayfinder">', false)
            ->assertSee('class="phiki', false)
            ->assertSee('<title>Localized routes in Vue without passing lang every time · goodcat.dev</title>', false)
            ->assertDontSee('keep reading');
    }

    public function test_italian_post_page_renders_the_translation(): void
    {
        $this->get('/it/blog/route-localizzate-in-vue')
            ->assertOk()
            ->assertSee('Scegli il tuo stack')
            ->assertSee('// prima di iniziare')
            ->assertSee('href="/it/docs/laravel-l10n"', false);
    }

    public function test_post_links_to_its_translation_with_the_translated_slug(): void
    {
        $this->get('/blog/localized-routes-in-vue')
            ->assertSee('<link rel="alternate" hreflang="it" href="'.url('/it/blog/route-localizzate-in-vue').'"', false)
            ->assertSee('href="'.url('/it/blog/route-localizzate-in-vue').'"', false);

        $this->get('/it/blog/route-localizzate-in-vue')
            ->assertSee('<link rel="alternate" hreflang="en" href="'.url('/blog/localized-routes-in-vue').'"', false);
    }

    public function test_unknown_post_returns_not_found(): void
    {
        $this->get('/blog/missing-post')->assertNotFound();
    }

    public function test_slug_of_another_locale_returns_not_found(): void
    {
        $this->get('/it/blog/localized-routes-in-vue')->assertNotFound();
        $this->get('/blog/route-localizzate-in-vue')->assertNotFound();
    }

    public function test_package_docs_redirect_to_the_current_version(): void
    {
        $this->get('/docs/laravel-l10n')
            ->assertRedirect(route('docs.show', ['laravel-l10n', '0.x', 'introduction']));
    }

    public function test_docs_introduction_renders_hero_guides_and_features(): void
    {
        $this->get('/docs/laravel-l10n/0.x/introduction')
            ->assertOk()
            ->assertSee('composer require goodcat/laravel-l10n')
            ->assertSee('<h2 id="at-a-glance">', false)
            ->assertSee('role="tablist"', false)
            ->assertSee('Read the user’s preferred locales')
            ->assertSee(route('docs.show', ['laravel-l10n', '0.x', 'quickstart']))
            ->assertSee('--pkg-main: var(--color-mint)', false);
    }

    public function test_every_page_in_the_navigation_renders(): void
    {
        foreach (['quickstart', 'localized-routes', 'url-generation', 'locale-preference', 'helpers', 'components', 'localized-views', 'javascript'] as $page) {
            $this->get("/docs/laravel-l10n/0.x/{$page}")->assertOk()->assertSee('class="phiki', false);
        }
    }

    public function test_docs_page_renders_alerts_tables_and_section_links(): void
    {
        $this->get('/docs/laravel-l10n/0.x/localized-routes')
            ->assertOk()
            ->assertSeeInOrder(['// routing', 'Localized Routes'])
            ->assertSee('// warning')
            ->assertSee('class="table-wrap"', false)
            ->assertSee('href="#route-strategy"', false)
            ->assertSee('<h2 id="route-strategy">', false)
            ->assertSee(route('docs.show', ['laravel-l10n', '0.x', 'url-generation']));
    }

    public function test_unknown_docs_return_not_found(): void
    {
        $this->get('/docs/missing-package')->assertNotFound();
        $this->get('/docs/missing-package/0.x/introduction')->assertNotFound();
        $this->get('/docs/laravel-l10n/0.x/missing-page')->assertNotFound();
        $this->get('/docs/laravel-l10n/2.x/introduction')->assertNotFound();
    }
}
