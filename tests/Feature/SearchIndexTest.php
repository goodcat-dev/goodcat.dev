<?php

namespace Tests\Feature;

use App\Content\Search\DocsRecords;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Tests\TestCase;

class SearchIndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Sleep::fake();

        config([
            'services.algolia.app_id' => 'APP',
            'services.algolia.write_key' => 'write-key',
            'services.algolia.search_key' => 'search-key',
            'services.algolia.index' => 'testing_content',
        ]);
    }

    protected function tearDown(): void
    {
        File::delete(storage_path('framework/search/testing_content.hash'));

        parent::tearDown();
    }

    public function test_records_are_one_per_section_with_anchor_and_hierarchy(): void
    {
        $records = collect(app(DocsRecords::class)->all());

        $record = $records->firstWhere('objectID', 'docs:laravel-l10n:0.x:en:localized-routes#route-strategy');

        $this->assertSame('/docs/laravel-l10n/0.x/localized-routes#route-strategy', $record['url']);
        $this->assertSame(['lvl0' => 'routing', 'lvl1' => 'Localized Routes', 'lvl2' => 'Route strategy', 'lvl3' => null], $record['hierarchy']);
        $this->assertStringContainsString('prefix_except_default', $record['content']);
        $this->assertStringNotContainsString('vendor:publish --tag=l10n-config', $record['content']);
        $this->assertSame('Custom binding keys', $records->firstWhere('objectID', 'docs:laravel-l10n:0.x:en:localized-routes#custom-binding-keys')['hierarchy']['lvl3']);
        $this->assertTrue($records->every(fn (array $record) => strlen(json_encode($record)) < 10_000));

        $italian = $records->firstWhere('objectID', 'docs:laravel-l10n:0.x:it:localized-routes#strategia-di-routing');

        $this->assertSame('/it/docs/laravel-l10n/0.x/localized-routes#strategia-di-routing', $italian['url']);
        $this->assertSame(['lvl0' => 'routing', 'lvl1' => 'Route localizzate', 'lvl2' => 'Strategia di routing', 'lvl3' => null], $italian['hierarchy']);
    }

    public function test_search_index_rebuilds_a_temporary_index_and_moves_it(): void
    {
        $this->fakeAlgolia();

        $this->artisan('search:index')->assertSuccessful();

        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && str_ends_with($request->url(), '/1/indexes/testing_content_tmp/batch')
            && $request->hasHeader('X-Algolia-API-Key', 'write-key')
            && collect($request['requests'])->contains('body.objectID', 'docs:laravel-l10n:0.x:en:quickstart#installation'));

        Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/1/indexes/testing_content_tmp/operation')
            && $request['operation'] === 'move'
            && $request['destination'] === 'testing_content');
    }

    public function test_search_index_skips_unchanged_content(): void
    {
        $this->fakeAlgolia();

        $this->artisan('search:index')->assertSuccessful();
        $sent = count(Http::recorded());

        $this->artisan('search:index')
            ->expectsOutputToContain('testing_content is up to date')
            ->assertSuccessful();

        $this->assertCount($sent, Http::recorded());
    }

    public function test_search_index_rebuilds_unchanged_content_when_forced(): void
    {
        $this->fakeAlgolia();

        $this->artisan('search:index')->assertSuccessful();
        $sent = count(Http::recorded());

        $this->artisan('search:index', ['--force' => true])->assertSuccessful();

        $this->assertGreaterThan($sent, count(Http::recorded()));
    }

    public function test_search_index_fails_when_a_task_is_never_published(): void
    {
        Http::fake([
            '*/task/*' => Http::response(['status' => 'notPublished']),
            '*' => Http::response(['taskID' => 1]),
        ]);

        $this->artisan('search:index')
            ->expectsOutputToContain('was not published within 60 seconds')
            ->assertFailed();

        Sleep::assertSleptTimes(120);
        $this->assertFileDoesNotExist(storage_path('framework/search/testing_content.hash'));
    }

    public function test_search_index_is_skipped_without_credentials(): void
    {
        config(['services.algolia.write_key' => null]);
        Http::fake();

        $this->artisan('search:index')
            ->expectsOutputToContain('Algolia is not configured')
            ->assertSuccessful();

        Http::assertNothingSent();
    }

    public function test_docs_header_exposes_only_the_search_key(): void
    {
        $this->get('/docs/laravel-l10n/0.x/quickstart')
            ->assertSee('Search the docs')
            ->assertSee('search-key')
            ->assertDontSee('write-key');
    }

    public function test_docs_search_is_shown_but_disabled_without_credentials(): void
    {
        config(['services.algolia.search_key' => null]);

        $this->get('/docs/laravel-l10n/0.x/quickstart')
            ->assertOk()
            ->assertSee('Search the docs')
            ->assertSee('\u0022enabled\u0022:false', false);
    }

    private function fakeAlgolia(): void
    {
        Http::fake([
            '*/task/*' => Http::response(['status' => 'published']),
            '*' => Http::response(['taskID' => 1]),
        ]);
    }
}
