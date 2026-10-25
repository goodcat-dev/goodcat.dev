<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ContentCacheTest extends TestCase
{
    public function test_content_cache_compiles_every_markdown_file_and_the_posts_index(): void
    {
        $this->artisan('content:clear')->assertSuccessful();

        $this->artisan('content:cache')->assertSuccessful();

        $this->assertCount(
            count(File::glob(base_path('content/docs/*/*/*/*.md'))) + count(File::glob(base_path('content/posts/*/*.md'))) + 1,
            File::files(storage_path('framework/content')),
        );
        $this->assertFileExists(storage_path('framework/content/posts.php'));
    }

    public function test_content_cache_keeps_unchanged_compiled_files(): void
    {
        $this->artisan('content:cache')->assertSuccessful();

        $compiled = collect(File::files(storage_path('framework/content')))
            ->reject(fn ($file) => $file->getFilename() === 'posts.php')
            ->map(fn ($file) => $file->getPathname());
        $marker = time() + 3600;

        $compiled->each(fn (string $path) => touch($path, $marker));

        $this->artisan('content:cache')->assertSuccessful();

        clearstatcache();
        $compiled->each(fn (string $path) => $this->assertSame($marker, filemtime($path), "{$path} was compiled again."));

        $this->artisan('content:clear')->assertSuccessful();
    }

    public function test_content_clear_removes_compiled_files(): void
    {
        $this->artisan('content:cache')->assertSuccessful();

        $this->artisan('content:clear')->assertSuccessful();

        $this->assertDirectoryDoesNotExist(storage_path('framework/content'));
    }
}
