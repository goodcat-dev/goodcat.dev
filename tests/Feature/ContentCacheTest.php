<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ContentCacheTest extends TestCase
{
    public function test_content_cache_compiles_every_markdown_file(): void
    {
        $this->artisan('content:clear')->assertSuccessful();

        $this->artisan('content:cache')->assertSuccessful();

        $this->assertCount(
            count(File::glob(base_path('content/docs/*/*/*/*.md'))),
            File::files(storage_path('framework/content')),
        );
    }

    public function test_content_clear_removes_compiled_files(): void
    {
        $this->artisan('content:cache')->assertSuccessful();

        $this->artisan('content:clear')->assertSuccessful();

        $this->assertDirectoryDoesNotExist(storage_path('framework/content'));
    }
}
