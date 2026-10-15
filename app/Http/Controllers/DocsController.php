<?php

namespace App\Http\Controllers;

use App\Content\Docs;
use App\Support\Translations;
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

    public function show(Docs $docs, Translations $translations, string $package, string $version, string $page): View
    {
        abort_unless(config()->has("packages.{$package}"), 404);

        $locale = app()->getLocale();
        $docsPage = $docs->find($package, $version, $page, $locale);

        abort_if($docsPage === null, 404);

        foreach (array_keys(config('app.locales')) as $other) {
            if ($other !== $locale && $docs->find($package, $version, $page, $other) === null) {
                $translations->missing($other);
            }
        }

        $navigation = $docs->navigation($package, $version, $locale);
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
