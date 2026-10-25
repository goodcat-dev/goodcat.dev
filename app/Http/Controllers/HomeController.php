<?php

namespace App\Http\Controllers;

use App\Content\Docs;
use App\Content\Posts;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(Docs $docs, Posts $posts): View
    {
        return view('home', [
            'packages' => collect(config('packages'))->map(fn (array $package, string $slug) => [
                ...$package,
                'url' => $docs->url($slug) ?? $package['repository'],
            ]),
            'posts' => $posts->all(app()->getLocale())->take(4),
        ]);
    }
}
