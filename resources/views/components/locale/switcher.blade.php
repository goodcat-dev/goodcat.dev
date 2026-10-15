@props(['align' => 'right'])

@php
    $translations = app(\App\Support\Translations::class)->all();
@endphp

@if (count($translations) > 1)
    <div x-data="disclosure" x-on:click.outside="open = false" x-on:keydown.escape="open = false" {{ $attributes->class('relative') }}>
        <button type="button" x-on:click="toggle" x-bind:aria-expanded="open" aria-haspopup="true" aria-label="Language: {{ $translations[app()->getLocale()]['label'] ?? app()->getLocale() }}" class="flex h-11 cursor-pointer items-center gap-2 rounded-full bg-ink-3 pr-3 pl-3.5 font-mono text-[13px] text-fog uppercase hover:text-chalk">
            <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21a9 9 0 1 0 0-18m0 18a9 9 0 1 1 0-18m0 18c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m-8.716 6h17.432M3.284 15h17.432" /></svg>
            {{ app()->getLocale() }}
            <svg class="size-3.5 transition-transform" x-bind:class="open && 'rotate-180'" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 8 5 5 5-5" /></svg>
        </button>
        <ul x-show="open" x-cloak x-transition @class([
            'absolute z-40 mt-2 w-56 rounded-[24px] bg-ink-2 p-2 shadow-[inset_0_0_0_1px_var(--color-line),0_20px_50px_-20px_rgba(0,0,0,0.8)] squircle',
            'right-0' => $align === 'right',
            'left-0' => $align === 'left',
            'left-0 sm:right-0 sm:left-auto' => $align === 'responsive',
        ])>
            <x-locale.options :translations="$translations" />
        </ul>
    </div>
@endif
