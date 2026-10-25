@props(['package', 'version', 'versions'])

@if (count($versions) > 1)
    <div x-data="disclosure" x-on:click.outside="open = false" x-on:keydown.escape.stop="open = false" {{ $attributes->class('relative') }}>
        <button type="button" x-on:click="toggle" x-bind:aria-expanded="open" aria-haspopup="true" aria-label="{{ __('Version: :version', ['version' => $version]) }}" class="flex h-11 w-full cursor-pointer items-center gap-2 rounded-full bg-ink-3 pr-3 pl-4 text-[15px] text-fog hover:text-chalk">
            {{ __('Version') }}
            <span class="font-mono text-[13px] text-chalk">{{ $version }}</span>
            <svg class="ml-auto size-3.5 transition-transform" x-bind:class="open && 'rotate-180'" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 8 5 5 5-5" /></svg>
        </button>
        <ul x-show="open" x-cloak x-transition class="absolute z-40 mt-2 w-full rounded-[24px] bg-ink-2 p-2 shadow-[inset_0_0_0_1px_var(--color-line),0_20px_50px_-20px_rgba(0,0,0,0.8)] squircle">
            @foreach ($versions as $candidate => $url)
                <li>
                    @if ($candidate === $version)
                        <span aria-current="true" class="flex items-center gap-3 rounded-[16px] bg-ink-4 px-3.5 py-2.5 font-mono text-[13px] font-bold">
                            {{ $candidate }}
                            @if ($candidate === $package['current_version'])
                                <span class="font-sans text-xs font-normal text-mist">{{ __('latest') }}</span>
                            @endif
                            <svg class="ml-auto size-4 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4.5 12.75 6 6 9-13.5" /></svg>
                        </span>
                    @else
                        <a href="{{ $url }}" class="flex items-center gap-3 rounded-[16px] px-3.5 py-2.5 font-mono text-[13px] text-fog hover:bg-ink-3 hover:text-chalk">
                            {{ $candidate }}
                            @if ($candidate === $package['current_version'])
                                <span class="font-sans text-xs text-mist">{{ __('latest') }}</span>
                            @endif
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endif
