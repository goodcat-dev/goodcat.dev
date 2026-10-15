<?php

use App\Http\Controllers\DocsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/blog/{slug}', [PostController::class, 'show'])->name('posts.show');

Route::get('/docs/{package}', [DocsController::class, 'index'])->name('docs.index');
Route::get('/docs/{package}/{version}/{page}', [DocsController::class, 'show'])
    ->where('version', '\d+\.x')
    ->name('docs.show');
