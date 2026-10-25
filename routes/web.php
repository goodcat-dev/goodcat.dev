<?php

use App\Http\Controllers\DocsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Middleware\SetHttpCacheHeaders;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

Route::lang(array_keys(config('app.locales')))
    ->middleware(SetHttpCacheHeaders::class.':public;max_age=300;s_maxage=604800;etag')
    ->withoutMiddleware([StartSession::class, ShareErrorsFromSession::class, PreventRequestForgery::class])
    ->group(function () {
        Route::get('/', HomeController::class)->name('home');

        Route::get('/blog', [PostController::class, 'index'])->name('posts.index');
        Route::get('/blog/{post}', [PostController::class, 'show'])
            ->where('post', '[a-z0-9-]+')
            ->name('posts.show');

        Route::get('/docs/{package}', [DocsController::class, 'index'])
            ->where('package', '[a-z0-9-]+')
            ->name('docs.index');
        Route::get('/docs/{package}/{version}/{page}', [DocsController::class, 'show'])
            ->where(['package' => '[a-z0-9-]+', 'version' => '\d+\.x', 'page' => '[a-z0-9-]+'])
            ->name('docs.show');
    });
