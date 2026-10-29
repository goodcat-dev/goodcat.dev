<?php

namespace Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\SyncsPosts;
use Tests\TestCase;

class CdnCachingTest extends TestCase
{
    use SyncsPosts;

    /**
     * @return array<string, array{string}>
     */
    public static function pages(): array
    {
        return [
            'home' => ['/'],
            'italian home' => ['/it'],
            'blog' => ['/blog'],
            'post' => ['/blog/localized-routes-in-vue'],
            'docs page' => ['/docs/laravel-l10n/0.x/introduction'],
        ];
    }

    #[DataProvider('pages')]
    public function test_public_pages_are_cacheable_by_the_cdn(string $uri): void
    {
        config(['app.http_cache' => true]);

        $response = $this->get($uri)->assertOk();

        $this->assertSame('max-age=300, public, s-maxage=604800', $response->headers->get('Cache-Control'));
        $this->assertNotNull($response->headers->get('ETag'));
        $this->assertSame([], $response->headers->getCookies());
    }

    public function test_pages_are_not_cached_when_http_cache_is_disabled(): void
    {
        config(['app.http_cache' => false]);

        $response = $this->get('/blog')->assertOk();

        $this->assertStringContainsString('no-cache', $response->headers->get('Cache-Control'));
        $this->assertNull($response->headers->get('ETag'));
        $this->assertSame([], $response->headers->getCookies());
    }

    public function test_routes_outside_the_public_group_keep_session_and_csrf(): void
    {
        Route::middleware('web')->get('/stateful', fn () => 'ok');

        $cookies = collect($this->get('/stateful')->assertOk()->headers->getCookies())->map->getName();

        $this->assertContains(config('session.cookie'), $cookies);
        $this->assertContains('XSRF-TOKEN', $cookies);
    }

    public function test_purge_clears_the_whole_cloudflare_zone(): void
    {
        config(['services.cloudflare' => ['zone_id' => 'zone-123', 'api_token' => 'secret']]);
        Http::fake(['api.cloudflare.com/*' => Http::response(['success' => true])]);

        $this->artisan('cdn:purge')->assertSuccessful();

        Http::assertSent(fn (Request $request) => $request->url() === 'https://api.cloudflare.com/client/v4/zones/zone-123/purge_cache'
            && $request->hasHeader('Authorization', 'Bearer secret')
            && $request['purge_everything'] === true);
    }

    public function test_purge_fails_when_cloudflare_rejects_the_request(): void
    {
        config(['services.cloudflare' => ['zone_id' => 'zone-123', 'api_token' => 'wrong']]);
        Http::fake(['api.cloudflare.com/*' => Http::response(['success' => false, 'errors' => [['message' => 'Authentication error']]], 403)]);

        $this->artisan('cdn:purge')
            ->expectsOutputToContain('Authentication error')
            ->assertFailed();
    }

    public function test_purge_is_skipped_without_credentials(): void
    {
        config(['services.cloudflare' => ['zone_id' => null, 'api_token' => null]]);
        Http::fake();

        $this->artisan('cdn:purge')->assertSuccessful();

        Http::assertNothingSent();
    }
}
