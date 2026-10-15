<?php

namespace App\Providers;

use App\Content\ContentCompiler;
use App\Content\Docs;
use App\Content\Markdown\MarkdownRenderer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ContentCompiler::class, fn ($app) => new ContentCompiler(
            $app->make(Filesystem::class),
            $app->make(MarkdownRenderer::class),
            storage_path('framework/content'),
        ));

        $this->app->singleton(Docs::class, fn ($app) => new Docs(
            $app->make(Filesystem::class),
            $app->make(ContentCompiler::class),
            base_path('content/docs'),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->optimizes(optimize: 'content:cache', clear: 'content:clear', key: 'content');
    }
}
