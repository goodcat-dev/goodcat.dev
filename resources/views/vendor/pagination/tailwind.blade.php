@if ($paginator->hasPages())
    @php
        $item = 'flex h-11 min-w-11 items-center justify-center rounded-full px-4';
        $arrowPrevious = '<svg class="size-[18px]" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 10H5M9 6l-4 4 4 4" /></svg>';
        $arrowNext = '<svg class="size-[18px]" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 10h10M11 6l4 4-4 4" /></svg>';
    @endphp

    <nav aria-label="{{ __('Pagination') }}" class="flex flex-wrap items-center justify-between gap-4">
        @if ($paginator->onFirstPage())
            <span aria-disabled="true" class="{{ $item }} gap-2 bg-ink-3 text-mist/60">{!! $arrowPrevious !!} {{ __('Previous') }}</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $item }} gap-2 bg-ink-3 font-bold hover:text-accent">{!! $arrowPrevious !!} {{ __('Previous') }}</a>
        @endif

        <ul class="hidden flex-wrap gap-1.5 rounded-full bg-ink-3 p-1 font-mono text-[13px] sm:flex">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="flex h-9 min-w-9 items-center justify-center text-mist">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="flex h-9 min-w-9 items-center justify-center rounded-full bg-chalk px-3 font-medium text-ink">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" aria-label="{{ __('Page :page', ['page' => $page]) }}" class="flex h-9 min-w-9 items-center justify-center rounded-full px-3 hover:text-accent">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach
        </ul>

        <span class="font-mono text-[13px] text-mist sm:hidden">{{ __('Page :page of :last', ['page' => $paginator->currentPage(), 'last' => $paginator->lastPage()]) }}</span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $item }} gap-2 bg-ink-3 font-bold hover:text-accent">{{ __('Next') }} {!! $arrowNext !!}</a>
        @else
            <span aria-disabled="true" class="{{ $item }} gap-2 bg-ink-3 text-mist/60">{{ __('Next') }} {!! $arrowNext !!}</span>
        @endif
    </nav>
@endif
