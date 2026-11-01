@props(['title' => null, 'description' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="motion-safe:scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <script>
            const applyTheme = () => {
                try {
                    document.documentElement.dataset.theme = localStorage.getItem('theme') ?? (matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
                } catch {
                    document.documentElement.dataset.theme = 'dark';
                }
            };

            applyTheme();
            addEventListener('pagereveal', applyTheme);
        </script>

        <title>{{ $title ? "{$title} · goodcat.dev" : 'goodcat.dev · Design, Code, Repeat.' }}</title>
        @if ($description)
            <meta name="description" content="{{ $description }}">
        @endif

        <link rel="icon" href="/favicon.ico" sizes="48x48">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="icon" href="/favicon-96x96.png" type="image/png" sizes="96x96">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <x-l10n::alternate />
        <link rel="alternate" type="application/atom+xml" title="goodcat.dev" href="{{ route('posts.feed') }}">

        <script type="speculationrules">
            {
                "prerender": [{
                    "where": {
                        "and": [
                            { "href_matches": "/*" },
                            { "not": { "selector_matches": "[rel~=nofollow], [target=_blank], [download]" } }
                        ]
                    },
                    "eagerness": "moderate"
                }]
            }
        </script>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body {{ $attributes }}>
        {{ $slot }}
    </body>
</html>
