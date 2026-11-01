<nav aria-label="Footer" {{ $attributes->class('flex flex-wrap items-center gap-2') }}>
    <a href="{{ route('privacy') }}" class="flex h-11 items-center rounded-full px-4 hover:bg-on-brand/10">Privacy</a>
    <a href="https://github.com/goodcat-dev" aria-label="GitHub" class="flex size-11 items-center justify-center rounded-full hover:bg-on-brand/10">
        <x-icons.github class="size-6" />
    </a>
    <a href="https://medium.com/@matteo.palazzo" aria-label="Medium" class="flex size-11 items-center justify-center rounded-full hover:bg-on-brand/10">
        <x-icons.medium class="size-6" />
    </a>
</nav>
