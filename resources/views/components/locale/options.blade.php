@props(['translations'])

@foreach ($translations as $locale => $translation)
    <li>
        @if ($translation['current'])
            <span aria-current="true" class="flex items-center justify-between gap-3 rounded-[16px] bg-ink-4 px-3.5 py-2.5 font-bold">
                <span lang="{{ $locale }}">{{ $translation['label'] }}</span>
                <svg class="size-4 text-acid" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4.5 12.75 6 6 9-13.5" /></svg>
            </span>
        @elseif ($translation['url'])
            <a href="{{ $translation['url'] }}" hreflang="{{ $locale }}" lang="{{ $locale }}" class="flex rounded-[16px] px-3.5 py-2.5 text-fog hover:bg-ink-3 hover:text-chalk">{{ $translation['label'] }}</a>
        @else
            <span aria-disabled="true" class="flex flex-col rounded-[16px] px-3.5 py-2.5 text-mist">
                <span lang="{{ $locale }}">{{ $translation['label'] }}</span>
                <span class="font-mono text-[11px]">not translated yet</span>
            </span>
        @endif
    </li>
@endforeach
