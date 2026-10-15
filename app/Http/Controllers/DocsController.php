<?php

namespace App\Http\Controllers;

use App\Content\Docs;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class DocsController extends Controller
{
    public function index(string $package): RedirectResponse
    {
        $version = config("packages.{$package}.current_version");

        abort_if($version === null, 404);

        return redirect()->route('docs.show', [$package, $version, 'introduction']);
    }

    public function show(Docs $docs, string $package, string $version, string $page): View
    {
        abort_unless(config()->has("packages.{$package}"), 404);

        $docsPage = $docs->find($package, $version, $page);

        abort_if($docsPage === null, 404);

        $navigation = $docs->navigation($package, $version);
        $titles = Arr::collapse($navigation);
        $slugs = array_keys($titles);
        $position = array_search($page, $slugs, true);

        return view('docs.show', [
            'package' => [...config("packages.{$package}"), 'slug' => $package],
            'version' => $version,
            'page' => $docsPage,
            'navigation' => $navigation,
            'titles' => $titles,
            'section' => collect($navigation)->search(fn (array $pages) => array_key_exists($page, $pages)) ?: null,
            'next' => $position !== false ? ($slugs[$position + 1] ?? null) : null,
        ]);
    }
}
