<x-code-tabs :files="array_map(fn (array $block) => $block['title'] ?? $block['language'], $blocks)">
    @foreach ($blocks as $block)
        <x-code-tabs.panel :index="$loop->index">{!! $block['html'] !!}</x-code-tabs.panel>
    @endforeach
</x-code-tabs>
