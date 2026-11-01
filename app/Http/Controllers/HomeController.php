<?php

namespace App\Http\Controllers;

use App\Content\Docs;
use App\Models\Post;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(Docs $docs): View
    {
        return view('home', [
            'packages' => collect(config('packages'))->map(fn (array $package, string $slug) => [
                ...$package,
                'url' => $docs->url($slug) ?? $package['repository'],
            ]),
            'posts' => Post::query()->whereNotNull('slug->'.app()->getLocale())->latest('published_at')->take(4)->get(),
        ]);
    }
}
