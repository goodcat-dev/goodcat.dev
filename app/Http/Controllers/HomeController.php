<?php

namespace App\Http\Controllers;

use App\Content\Docs;
use App\Support\SamplePosts;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(Docs $docs): View
    {
        return view('home', [
            'packages' => collect(config('packages'))->map(fn (array $package, string $slug) => [
                ...$package,
                'url' => $docs->exists($slug, $package['current_version'])
                    ? route('docs.index', $slug)
                    : $package['repository'],
            ]),
            'posts' => SamplePosts::all()->take(4),
        ]);
    }
}
