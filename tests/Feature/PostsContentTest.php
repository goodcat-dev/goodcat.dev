<?php

namespace Tests\Feature;

use App\Content\Post;
use App\Content\Posts;
use Tests\TestCase;

class PostsContentTest extends TestCase
{
    public function test_every_locale_has_the_same_posts(): void
    {
        $posts = app(Posts::class);
        $keys = fn (string $locale) => $posts->all($locale)->map(fn (Post $post) => $post->key)->sort()->values()->all();
        $reference = config('app.fallback_locale');

        foreach (array_keys(config('app.locales')) as $locale) {
            $this->assertSame($keys($reference), $keys($locale), "Posts in {$locale} differ from {$reference}.");
        }
    }

    public function test_translations_share_date_package_and_tags(): void
    {
        $posts = app(Posts::class);

        foreach ($posts->all(config('app.fallback_locale')) as $post) {
            foreach (array_keys(config('app.locales')) as $locale) {
                $translation = $posts->find($post->key, $locale);

                $this->assertTrue($post->publishedAt->equalTo($translation->publishedAt), "{$locale}/{$post->slug} has a different date.");
                $this->assertSame($post->package, $translation->package, "{$locale}/{$post->slug} has a different package.");
                $this->assertSame($post->tags, $translation->tags, "{$locale}/{$post->slug} has different tags.");
            }
        }
    }

    public function test_slugs_are_unique_within_each_locale(): void
    {
        $posts = app(Posts::class);

        foreach (array_keys(config('app.locales')) as $locale) {
            $slugs = $posts->all($locale)->map(fn (Post $post) => $post->slug);

            $this->assertSame($slugs->unique()->count(), $slugs->count(), "Duplicate slugs in {$locale}.");
            $this->assertTrue($slugs->every(fn (string $slug) => preg_match('/^[a-z0-9-]+$/', $slug) === 1), "Invalid slug in {$locale}.");
        }
    }

    public function test_post_packages_exist(): void
    {
        foreach (app(Posts::class)->all() as $post) {
            if ($post->package !== null) {
                $this->assertTrue(config()->has("packages.{$post->package}"), "{$post->slug} refers to an unknown package.");
            }
        }
    }
}
