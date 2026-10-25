<?php

namespace App\Providers;

use App\Content\ContentCompiler;
use App\Content\Docs;
use App\Content\Markdown\MarkdownRenderer;
use App\Content\Posts;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Route;
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

        $this->app->singleton(Posts::class, fn ($app) => new Posts(
            $app->make(Filesystem::class),
            $app->make(ContentCompiler::class),
            base_path('content/posts'),
            storage_path('framework/content/posts.php'),
            ! $app->isProduction(),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::bind('post', fn (string $slug) => $this->app->make(Posts::class)->findBySlug($slug, $this->app->getLocale()) ?? abort(404));

        $this->optimizes(optimize: 'content:cache', clear: 'content:clear', key: 'content');
    }
}
