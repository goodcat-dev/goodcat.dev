<aside class="mt-12 rounded-[40px] bg-ink-3 p-8 squircle [&_figure]:bg-ink [&>span+*]:mt-3">
    @if ($label)
        <span class="font-mono text-[13px] text-accent">// {{ $label }}</span>
    @endif
    {!! $html !!}
</aside>
