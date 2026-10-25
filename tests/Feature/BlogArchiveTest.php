<?php

namespace Tests\Feature;

use App\Content\ContentCompiler;
use App\Content\Markdown\MarkdownRenderer;
use App\Content\Posts;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BlogArchiveTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->directory = storage_path('framework/testing/blog-archive');

        foreach (['en', 'it'] as $locale) {
            File::ensureDirectoryExists("{$this->directory}/posts/{$locale}");

            foreach (range(1, 25) as $number) {
                $date = now()->subDays($number)->toDateString();
                $slug = $locale === 'en' ? "post-{$number}" : "articolo-{$number}";

                File::put("{$this->directory}/posts/{$locale}/post-{$number}.md", "---\ntitle: 'Post {$number}'\nslug: {$slug}\npublished_at: {$date}\n---\n\nBody.\n");
            }
        }

        $this->app->instance(Posts::class, $this->posts());
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->directory);

        parent::tearDown();
    }

    public function test_archive_is_paginated_from_the_newest_post(): void
    {
        $this->get(route('posts.index'))
            ->assertOk()
            ->assertSee('25 posts')
            ->assertSeeInOrder(['Post 1', 'Post 2', 'Post 20'])
            ->assertDontSee('Post 21')
            ->assertSee('href="'.route('posts.index', ['page' => 2]).'"', false)
            ->assertSee('rel="next"', false);

        $this->get(route('posts.index', ['page' => 2]))
            ->assertOk()
            ->assertSeeInOrder(['Post 21', 'Post 25'])
            ->assertDontSee('Post 20<', false)
            ->assertSee('rel="prev"', false);
    }

    public function test_pages_past_the_last_one_return_not_found(): void
    {
        $this->get(route('posts.index', ['page' => 3]))->assertNotFound();
    }

    public function test_italian_archive_links_to_translated_slugs(): void
    {
        $this->get('/it/blog?page=2')
            ->assertOk()
            ->assertSee('href="'.url('/it/blog/articolo-25').'"', false)
            ->assertSee('Successiva');
    }

    public function test_index_is_rebuilt_when_a_post_is_added(): void
    {
        $this->get(route('posts.index'))->assertSee('25 posts');

        File::put("{$this->directory}/posts/en/post-26.md", "---\ntitle: 'Post 26'\npublished_at: 2020-01-01\n---\n\nBody.\n");
        File::put("{$this->directory}/posts/it/post-26.md", "---\ntitle: 'Post 26'\npublished_at: 2020-01-01\n---\n\nBody.\n");
        $this->app->instance(Posts::class, $this->posts());

        $this->get(route('posts.index'))->assertSee('26 posts');
    }

    private function posts(): Posts
    {
        $files = new Filesystem;

        return new Posts(
            $files,
            new ContentCompiler($files, $this->app->make(MarkdownRenderer::class), "{$this->directory}/compiled"),
            "{$this->directory}/posts",
            "{$this->directory}/compiled/posts.php",
        );
    }
}
