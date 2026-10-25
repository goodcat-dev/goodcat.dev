@props(['compact' => false])

<footer class="mx-auto max-w-7xl px-5 pb-10 md:px-8">
    @if ($compact)
        <div class="flex flex-wrap items-center justify-between gap-6 rounded-[40px] bg-brand p-8 text-on-brand squircle md:rounded-[60px] md:p-12">
            <span class="text-[44px] leading-[0.9] font-extrabold tracking-[-0.05em] md:text-[64px]">goodcat.dev</span>
            <x-site.footer-links />
        </div>
    @else
        <div class="flex flex-col gap-10 rounded-[40px] bg-brand px-6 py-10 text-on-brand squircle md:rounded-[60px] md:px-12 md:py-14">
            <div class="flex flex-wrap items-center justify-between gap-8">
                <p class="text-[44px] leading-[0.85] font-extrabold tracking-[-0.06em] sm:text-[72px] lg:text-[150px]">goodcat.dev</p>
                <x-site.cat-mark class="size-24 shrink-0 rotate-8 lg:size-[170px]" />
            </div>
            <div class="flex flex-wrap justify-between gap-4 text-[17px] font-medium">
                <span>Design, Code, Repeat.</span>
                <x-site.footer-links />
            </div>
        </div>
    @endif
</footer>
