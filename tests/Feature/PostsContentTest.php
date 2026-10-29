<?php

namespace Tests\Feature;

use App\Content\ContentCompiler;
use App\Content\Markdown\MarkdownRenderer;
use App\Content\Posts;
use App\Models\Post;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PostsContentTest extends TestCase
{
    use RefreshDatabase;

    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->directory = storage_path('framework/testing/posts-sync');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->directory);

        parent::tearDown();
    }

    public function test_slugs_are_unique_within_each_locale(): void
    {
        app(Posts::class)->sync();

        foreach (array_keys(config('app.locales')) as $locale) {
            $slugs = Post::query()->translatedIn($locale)->get()->map(fn (Post $post) => $post->getTranslation('slug', $locale, false));

            $this->assertSame($slugs->unique()->count(), $slugs->count(), "Duplicate slugs in {$locale}.");
            $this->assertTrue($slugs->every(fn (string $slug) => preg_match('/^[a-z0-9-]+$/', $slug) === 1), "Invalid slug in {$locale}.");
        }
    }

    public function test_post_packages_exist(): void
    {
        app(Posts::class)->sync();

        foreach (Post::query()->whereNotNull('package')->get() as $post) {
            $this->assertTrue(config()->has("packages.{$post->package}"), "{$post->key} refers to an unknown package.");
        }
    }

    public function test_sync_stores_translations_and_tags_of_each_post(): void
    {
        $this->writePost('en', 'first-post', "title: 'First `post`'\npublished_at: 2026-01-10\npackage: laravel-l10n\ntags: [vue, ssr]");
        $this->writePost('it', 'first-post', "title: 'Primo `articolo`'\nslug: primo-articolo\npublished_at: 2026-01-10");

        $this->posts()->sync();

        $post = Post::query()->sole();

        $this->assertSame('first-post', $post->key);
        $this->assertSame(['en' => 'first-post', 'it' => 'primo-articolo'], $post->getTranslations('slug'));
        $this->assertSame('Primo articolo', $post->setLocale('it')->title);
        $this->assertSame('laravel-l10n', $post->package);
        $this->assertSame('2026-01-10', $post->published_at->toDateString());
        $this->assertSame(['vue', 'ssr'], $post->tags->pluck('name')->all());
    }

    public function test_sync_mirrors_removed_files_and_translations(): void
    {
        $this->writePost('en', 'kept', "title: Kept\npublished_at: 2026-01-10");
        $this->writePost('it', 'kept', "title: Tenuto\npublished_at: 2026-01-10");
        $this->writePost('en', 'removed', "title: Removed\npublished_at: 2026-01-11");
        $this->posts()->sync();

        File::delete(["{$this->directory}/posts/it/kept.md", "{$this->directory}/posts/en/removed.md"]);
        $this->posts()->sync();

        $post = Post::query()->sole();

        $this->assertSame('kept', $post->key);
        $this->assertSame(['en'], $post->getTranslatedLocales('slug'));
    }

    public function test_posts_are_stale_until_synced_and_after_a_file_changes(): void
    {
        $this->writePost('en', 'first-post', "title: First\npublished_at: 2026-01-10");
        $posts = $this->posts();

        $this->assertTrue($posts->isStale());

        $posts->sync();
        $this->assertFalse($posts->isStale());

        $this->writePost('it', 'first-post', "title: Primo\npublished_at: 2026-01-10");
        $this->assertTrue($posts->isStale());
    }

    public function test_posts_are_stale_when_the_renderer_changes(): void
    {
        $this->writePost('en', 'first-post', "title: First\npublished_at: 2026-01-10");
        File::put("{$this->directory}/view.blade.php", 'v1');
        $this->posts()->sync();

        $this->assertFalse($this->posts()->isStale());

        touch("{$this->directory}/view.blade.php", time() + 60);
        clearstatcache();

        $this->assertTrue($this->posts()->isStale());
    }

    private function writePost(string $locale, string $key, string $frontMatter): void
    {
        File::ensureDirectoryExists("{$this->directory}/posts/{$locale}");
        File::put("{$this->directory}/posts/{$locale}/{$key}.md", "---\n{$frontMatter}\n---\n\nBody.\n");
    }

    private function posts(): Posts
    {
        $files = new Filesystem;

        return new Posts(
            $files,
            new ContentCompiler($files, $this->app->make(MarkdownRenderer::class), "{$this->directory}/compiled", ["{$this->directory}/view.blade.php"]),
            "{$this->directory}/posts",
            "{$this->directory}/compiled/posts.json",
        );
    }
}
