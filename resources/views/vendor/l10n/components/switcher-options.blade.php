@foreach ($translations as $locale => $href)
    <li>
        @if ($locale === $current)
            <span aria-current="true" class="flex items-center justify-between gap-3 rounded-[16px] bg-ink-4 px-3.5 py-2.5 font-bold">
                <span lang="{{ $locale }}">{{ $labels[$locale] ?? $locale }}</span>
                <svg class="size-4 text-acid" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4.5 12.75 6 6 9-13.5" /></svg>
            </span>
        @else
            <a href="{{ $href }}" hreflang="{{ str_replace('_', '-', $locale) }}" lang="{{ $locale }}" class="flex rounded-[16px] px-3.5 py-2.5 text-fog hover:bg-ink-3 hover:text-chalk">{{ $labels[$locale] ?? $locale }}</a>
        @endif
    </li>
@endforeach
