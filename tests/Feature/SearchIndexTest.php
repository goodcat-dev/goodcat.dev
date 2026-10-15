<?php

namespace Tests\Feature;

use App\Content\Search\DocsRecords;
use Illuminate\Http\Client\Request;
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
            'services.algolia.admin_key' => 'admin-key',
            'services.algolia.search_key' => 'search-key',
            'services.algolia.index' => 'goodcat_content',
        ]);
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
    }

    public function test_search_index_rebuilds_a_temporary_index_and_moves_it(): void
    {
        Http::fake([
            '*/task/*' => Http::response(['status' => 'published']),
            '*' => Http::response(['taskID' => 1]),
        ]);

        $this->artisan('search:index')->assertSuccessful();

        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && str_ends_with($request->url(), '/1/indexes/goodcat_content_tmp/batch')
            && $request->hasHeader('X-Algolia-API-Key', 'admin-key')
            && collect($request['requests'])->contains('body.objectID', 'docs:laravel-l10n:0.x:en:quickstart#installation'));

        Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/1/indexes/goodcat_content_tmp/operation')
            && $request['operation'] === 'move'
            && $request['destination'] === 'goodcat_content');
    }

    public function test_search_index_fails_without_credentials(): void
    {
        config(['services.algolia.admin_key' => null]);
        Http::fake();

        $this->artisan('search:index')->assertFailed();

        Http::assertNothingSent();
    }

    public function test_docs_header_exposes_only_the_search_key(): void
    {
        $this->get('/docs/laravel-l10n/0.x/quickstart')
            ->assertSee('Search the docs')
            ->assertSee('search-key')
            ->assertDontSee('admin-key');
    }

    public function test_docs_search_is_shown_but_disabled_without_credentials(): void
    {
        config(['services.algolia.search_key' => null]);

        $this->get('/docs/laravel-l10n/0.x/quickstart')
            ->assertOk()
            ->assertSee('Search the docs')
            ->assertSee('\u0022enabled\u0022:false', false);
    }
}
