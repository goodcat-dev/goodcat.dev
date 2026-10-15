@props(['package', 'version', 'current', 'navigation', 'toc' => []])

<nav aria-label="{{ __('Documentation') }}" {{ $attributes->class('flex flex-col gap-7 text-[15px]') }}>
    @foreach ($navigation as $section => $pages)
        <div>
            <h2 class="mb-2 ml-3 font-mono text-xs font-normal text-mist">// {{ $section }}</h2>
            @foreach ($pages as $slug => $label)
                @if ($slug === $current)
                    <a href="{{ route('docs.show', [$package['slug'], $version, $slug]) }}" aria-current="page" class="block rounded-[14px] bg-pkg px-3 py-2.5 font-bold text-pkg-ink squircle hover:text-pkg-ink">{{ $label }}</a>
                    @if ($toc)
                        <div class="my-1.5 ml-[18px] flex flex-col border-l-2 border-line pl-3.5">
                            @foreach ($toc as $id => $heading)
                                <a href="#{{ $id }}" class="rounded-[10px] px-2.5 py-[7px] text-sm text-mist hover:bg-ink-3 hover:text-chalk">{{ $heading }}</a>
                            @endforeach
                        </div>
                    @endif
                @else
                    <a href="{{ route('docs.show', [$package['slug'], $version, $slug]) }}" class="block rounded-[14px] px-3 py-2.5 text-fog squircle hover:bg-ink-3">{{ $label }}</a>
                @endif
            @endforeach
        </div>
    @endforeach
</nav>
