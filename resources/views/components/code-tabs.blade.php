@props(['files'])

<div data-code x-data="copyCode" {{ $attributes->class('mt-6 overflow-hidden rounded-[28px] bg-ink-2 shadow-[inset_0_0_0_1px_var(--color-line)] squircle') }}>
    <div x-data="tabs(0)">
        <div class="flex items-center gap-1 border-b border-line pt-2.5 pr-2.5 pl-3.5">
            <div role="tablist" aria-label="Code example" class="flex flex-wrap gap-1">
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
            <button type="button" x-on:click="copy($el)" x-bind:aria-label="copied ? 'Copied' : 'Copy code'" aria-label="Copy code" class="ml-auto flex size-11 cursor-pointer items-center justify-center text-mist hover:text-chalk">
                <svg x-show="! copied" class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25" /></svg>
                <svg x-show="copied" x-cloak class="size-[18px] text-pkg-text" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4.5 12.75 6 6 9-13.5" /></svg>
            </button>
        </div>
        {{ $slot }}
    </div>
</div>
