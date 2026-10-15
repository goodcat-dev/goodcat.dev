<?php

namespace App\Http\Controllers;

use App\Support\SampleDocs;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DocsController extends Controller
{
    public function index(string $package): RedirectResponse
    {
        $version = config("packages.{$package}.current_version");

        abort_if($version === null, 404);

        return redirect()->route('docs.show', [$package, $version, 'introduction']);
    }

    public function show(string $package, string $version, string $page): View
    {
        $view = SampleDocs::view($package, $version, $page);

        abort_unless(config()->has("packages.{$package}") && view()->exists($view), 404);

        return view($view, [
            'package' => [...config("packages.{$package}"), 'slug' => $package],
            'version' => $version,
            'page' => $page,
            'navigation' => SampleDocs::navigation($package, $version),
        ]);
    }
}
