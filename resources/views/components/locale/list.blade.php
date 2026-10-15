@php
    $translations = app(\App\Support\Translations::class)->all();
@endphp

@if (count($translations) > 1)
    <div {{ $attributes }}>
        <h2 id="language-heading" class="mb-2 ml-3 font-mono text-xs font-normal text-mist">// language</h2>
        <ul aria-labelledby="language-heading" class="flex flex-col gap-1">
            <x-locale.options :translations="$translations" />
        </ul>
    </div>
@endif
