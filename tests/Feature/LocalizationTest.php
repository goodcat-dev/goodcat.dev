<?php

namespace Tests\Feature;

use App\Content\Docs;
use Tests\SyncsPosts;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use SyncsPosts;

    public function test_italian_pages_are_served_under_the_it_prefix(): void
    {
        $this->get('/it')
            ->assertOk()
            ->assertSee('<html lang="it"', false)
            ->assertSee('href="'.url('/it/blog/route-localizzate-in-vue').'"', false)
            ->assertSee('href="'.url('/it/docs/laravel-l10n').'"', false);
    }

    public function test_english_pages_stay_unprefixed(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<html lang="en"', false)
            ->assertSee('href="'.url('/blog/localized-routes-in-vue').'"', false);
    }

    public function test_english_home_renders_english_prose_and_dates(): void
    {
        $this->get('/')
            ->assertSee('I love designing solutions')
            ->assertSee('I follow the KISS philosophy')
            ->assertSee('Start reading')
            ->assertSee('Oct 2, 2026')
            ->assertSee('<meta name="description" content="Laravel articles by Matteo', false);
    }

    public function test_italian_home_renders_localized_prose_and_dates(): void
    {
        $this->get('/it')
            ->assertSee('Mi piace progettare soluzioni')
            ->assertSee('Seguo la filosofia KISS')
            ->assertSee('Inizia a leggere')
            ->assertSee('2 ott 2026')
            ->assertSee('<meta name="description" content="Articoli su Laravel di Matteo', false)
            ->assertSee('Route localizzate, URI e domini tradotti')
            ->assertDontSee('I follow the KISS philosophy');
    }

    public function test_blog_archive_counts_posts_in_the_current_language(): void
    {
        $this->get('/blog')->assertSee('1 post');
        $this->get('/it/blog')->assertSee('1 articolo');
    }

    public function test_italian_docs_are_served_with_translated_content_and_interface(): void
    {
        $this->get('/it/docs/laravel-l10n')
            ->assertRedirect(url('/it/docs/laravel-l10n/0.x/introduction'));

        $this->get('/it/docs/laravel-l10n/0.x/localized-routes')
            ->assertOk()
            ->assertSee('<h1 class="mt-3 text-5xl leading-[0.95] font-extrabold tracking-[-0.05em] md:text-[72px]">Route localizzate</h1>', false)
            ->assertSee('<h2 id="strategia-di-routing">', false)
            ->assertSee('// attenzione')
            ->assertSee('Cerca nella documentazione')
            ->assertSee('href="'.url('/it/docs/laravel-l10n/0.x/url-generation').'"', false);
    }

    public function test_compiled_content_keeps_the_language_of_its_file(): void
    {
        $this->artisan('content:clear');
        app()->setLocale('it');

        $english = app(Docs::class)->find('laravel-l10n', '0.x', 'localized-routes', 'en');
        $italian = app(Docs::class)->find('laravel-l10n', '0.x', 'localized-routes', 'it');

        $this->assertStringContainsString('// warning', $english->html);
        $this->assertStringNotContainsString('// attenzione', $english->html);
        $this->assertStringContainsString('// attenzione', $italian->html);
        $this->assertSame('it', app()->getLocale());
    }

    public function test_switcher_and_alternate_links_point_to_the_translations(): void
    {
        $this->get('/docs/laravel-l10n/0.x/quickstart')
            ->assertSee('href="'.url('/it/docs/laravel-l10n/0.x/quickstart').'" hreflang="it"', false)
            ->assertSee('<link rel="alternate" hreflang="it" href="'.url('/it/docs/laravel-l10n/0.x/quickstart').'" />', false)
            ->assertSee('<link rel="alternate" hreflang="x-default" href="'.url('/docs/laravel-l10n/0.x/quickstart').'" />', false);

        $this->get('/it')
            ->assertSee('href="'.url('/').'" hreflang="en"', false);
    }

    public function test_old_major_urls_are_not_found(): void
    {
        $this->get('/docs/laravel-l10n/1.x/introduction')->assertNotFound();
    }
}
