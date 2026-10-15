<aside @class([
    'mt-7 flex flex-col gap-2 rounded-[28px] p-6 squircle sm:flex-row sm:gap-4 [&_p]:mt-0! [&_p]:text-base! [&_p+p]:mt-3!',
    'bg-ink-2 shadow-[inset_0_0_0_2px_var(--pkg-main)]' => $type === 'note',
    'bg-tangerine/10 shadow-[inset_0_0_0_2px_var(--color-tangerine)]' => $type === 'warning',
])>
    <span @class(['shrink-0 pt-0.5 font-mono text-xs', 'text-pkg-text' => $type === 'note', 'text-tangerine' => $type === 'warning'])>// {{ $type }}</span>
    <div class="min-w-0">{!! $html !!}</div>
</aside>
