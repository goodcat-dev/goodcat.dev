<a href="{{ route('home') }}" aria-label="goodcat.dev home" {{ $attributes->class('flex items-center gap-3 text-[22px] font-extrabold tracking-[-0.03em] hover:text-acid') }}>
    <x-site.blob class="size-11 -rotate-6 text-ink">
        <x-site.cat-mark :stroke-width="4" class="size-[34px]" />
    </x-site.blob>
    <span aria-hidden="true" class="flex items-baseline">
        goodcat<span class="mx-[3px] inline-block size-1.5 -translate-y-[9px] rounded-full bg-acid"></span><span class="text-mist">dev</span>
    </span>
</a>
