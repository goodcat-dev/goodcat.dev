@php
    $translations = collect(app(\App\Support\Translations::class)->all())->filter(fn (array $translation) => $translation['url'] !== null);
@endphp

@if ($translations->count() > 1)
    @foreach ($translations as $locale => $translation)
        <link rel="alternate" hreflang="{{ $locale }}" href="{{ $translation['url'] }}">
    @endforeach
    @if ($default = $translations->get(config('app.fallback_locale')))
        <link rel="alternate" hreflang="x-default" href="{{ $default['url'] }}">
    @endif
@endif
