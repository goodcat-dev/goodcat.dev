<?php

namespace App\Providers;

use App\Content\ContentCompiler;
use App\Content\Docs;
use App\Content\Markdown\MarkdownRenderer;
use App\Content\Posts;
use App\Models\Post;
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
            [
                app_path('Content/Markdown'),
                resource_path('views/markdown'),
                resource_path('views/components/code-block.blade.php'),
                resource_path('views/components/code-tabs.blade.php'),
                resource_path('views/components/code-tabs'),
                resource_path('views/components/copy-button.blade.php'),
                lang_path(),
                base_path('composer.lock'),
            ],
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
            storage_path('framework/content/posts.json'),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::bind('post', fn (string $slug) => Post::query()->where('slug->'.$this->app->getLocale(), $slug)->firstOrFail());

        $this->optimizes(optimize: 'content:cache', clear: 'content:clear', key: 'content');

        if (! $this->app->isProduction() && ! $this->app->runningInConsole()) {
            $posts = $this->app->make(Posts::class);

            if ($posts->isStale()) {
                $posts->sync();
            }
        }
    }
}
