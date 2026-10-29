<aside class="mt-12 rounded-[28px] bg-ink-3 p-5 squircle md:rounded-[40px] md:p-8 [&_figure]:bg-ink [&>span+*]:mt-3">
    @if ($label)
        <span class="font-mono text-[13px] text-accent">// {{ $label }}</span>
    @endif
    {!! $html !!}
</aside>
