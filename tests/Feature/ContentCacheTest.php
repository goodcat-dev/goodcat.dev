<?php

namespace Tests\Feature;

use App\Content\ContentCompiler;
use App\Content\Markdown\MarkdownRenderer;
use App\Models\Post;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ContentCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_cache_compiles_every_markdown_file_and_syncs_the_posts(): void
    {
        $this->artisan('content:clear')->assertSuccessful();

        $this->artisan('content:cache')->assertSuccessful();

        $this->assertCount(
            count(File::glob(base_path('content/docs/*/*/*/*.md'))) + count(File::glob(base_path('content/posts/*/*.md'))) + 1,
            File::files(storage_path('framework/content')),
        );
        $this->assertFileExists(storage_path('framework/content/posts.json'));
        $this->assertSame(collect(File::glob(base_path('content/posts/*/*.md')))->map(fn (string $path) => basename($path))->unique()->count(), Post::query()->count());
    }

    public function test_content_cache_keeps_unchanged_compiled_files(): void
    {
        $this->artisan('content:cache')->assertSuccessful();

        $compiled = collect(File::files(storage_path('framework/content')))
            ->reject(fn ($file) => $file->getFilename() === 'posts.json')
            ->map(fn ($file) => $file->getPathname());
        $marker = time() + 3600;

        $compiled->each(fn (string $path) => touch($path, $marker));

        $this->artisan('content:cache')->assertSuccessful();

        clearstatcache();
        $compiled->each(fn (string $path) => $this->assertSame($marker, filemtime($path), "{$path} was compiled again."));

        $this->artisan('content:clear')->assertSuccessful();
    }

    public function test_content_is_recompiled_when_a_renderer_dependency_changes(): void
    {
        $directory = storage_path('framework/testing/content-version');
        File::ensureDirectoryExists($directory);
        File::put("{$directory}/page.md", "# Title\n");
        File::put("{$directory}/view.blade.php", 'v1');

        $compiler = fn () => new ContentCompiler(new Filesystem, app(MarkdownRenderer::class), "{$directory}/compiled", ["{$directory}/view.blade.php"]);

        $compiler()->get("{$directory}/page.md", 'en');
        $this->assertCount(1, File::files("{$directory}/compiled"));

        touch("{$directory}/view.blade.php", time() + 60);
        clearstatcache();

        $updated = $compiler();
        $updated->get("{$directory}/page.md", 'en');
        $this->assertCount(2, File::files("{$directory}/compiled"));

        $updated->prune(["{$directory}/page.md"]);
        $this->assertCount(1, File::files("{$directory}/compiled"));

        File::deleteDirectory($directory);
    }

    public function test_content_cache_removes_outdated_compiled_files(): void
    {
        File::ensureDirectoryExists(storage_path('framework/content'));
        File::put($orphan = storage_path('framework/content/'.str_repeat('a', 32).'.php'), '<?php return [];');

        $this->artisan('content:cache')->assertSuccessful();

        $this->assertFileDoesNotExist($orphan);
        $this->assertFileExists(storage_path('framework/content/posts.json'));
    }

    public function test_content_clear_removes_compiled_files(): void
    {
        $this->artisan('content:cache')->assertSuccessful();

        $this->artisan('content:clear')->assertSuccessful();

        $this->assertDirectoryDoesNotExist(storage_path('framework/content'));
    }
}
