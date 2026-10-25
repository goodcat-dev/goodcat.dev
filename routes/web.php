<?php

use App\Http\Controllers\DocsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::lang(array_keys(config('app.locales')))->group(function () {
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
