@props(['files'])

<div data-code x-data="copyCode" data-theme="dark" {{ $attributes->class('mt-7 overflow-hidden rounded-[28px] bg-ink-2 shadow-[inset_0_0_0_1px_var(--color-line)] squircle') }}>
    <div x-data="tabs(0)">
        <div class="flex items-center gap-1 border-b border-line pt-2.5 pr-1 pl-3.5">
            <div role="tablist" aria-label="{{ __('Code example') }}" class="flex min-w-0 flex-wrap gap-1">
                @foreach ($files as $file)
                    <button
                        type="button"
                        role="tab"
                        x-on:click="select({{ $loop->index }})"
                        x-bind:aria-selected="isActive({{ $loop->index }})"
                        x-bind:class="isActive({{ $loop->index }}) ? 'text-chalk shadow-[inset_0_-2px_0_var(--pkg-main)]' : 'text-mist hover:text-fog'"
                        class="h-11 cursor-pointer px-3.5 font-mono text-[13px]"
                    >{{ $file }}</button>
                @endforeach
            </div>
            <x-copy-button class="ml-auto self-start" />
        </div>
        {{ $slot }}
    </div>
</div>
