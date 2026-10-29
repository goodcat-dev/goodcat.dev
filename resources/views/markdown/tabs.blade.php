@php($labelId = 'tabs-'.Str::slug($label ?? 'tabs'))

<div x-data="tabs(@js($tabs[0]['id'] ?? null))">
    <div class="sticky top-4 z-10 mt-14 flex flex-wrap items-center justify-between gap-4 rounded-full bg-ink-3/92 py-2 pr-2 pl-6 backdrop-blur max-sm:w-fit max-sm:pl-2 light:bg-ink-2/92 light:shadow-[inset_0_0_0_1px_var(--color-line)]">
        <span id="{{ $labelId }}" class="text-base font-bold max-sm:sr-only">{{ $label }}</span>
        <div role="tablist" aria-labelledby="{{ $labelId }}" class="flex gap-1 rounded-full bg-ink p-1 light:bg-ink-3">
            @foreach ($tabs as $tab)
                <button
                    type="button"
                    role="tab"
                    id="tab-{{ $tab['id'] }}"
                    aria-controls="panel-{{ $tab['id'] }}"
                    x-on:click="select(@js($tab['id']))"
                    x-bind:aria-selected="isActive(@js($tab['id']))"
                    x-bind:class="isActive(@js($tab['id'])) ? 'bg-brand text-on-brand' : 'text-fog hover:text-chalk'"
                    class="h-11 cursor-pointer rounded-full px-[22px] text-base font-bold"
                >{{ $tab['label'] }}</button>
            @endforeach
        </div>
    </div>

    @foreach ($tabs as $tab)
        <section id="panel-{{ $tab['id'] }}" role="tabpanel" aria-labelledby="tab-{{ $tab['id'] }}" x-show="isActive(@js($tab['id']))" @unless ($loop->first) x-cloak @endunless>
            {!! $tab['html'] !!}
        </section>
    @endforeach
</div>
