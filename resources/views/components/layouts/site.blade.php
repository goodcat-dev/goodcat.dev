@props(['title' => null, 'description' => null, 'active' => null, 'compactFooter' => false])

<x-layouts.base :title="$title" :description="$description">
    <div class="min-h-screen overflow-hidden">
        <x-site.header :active="$active" />

        <main>
            {{ $slot }}
        </main>

        <x-site.footer :compact="$compactFooter" />
    </div>
</x-layouts.base>
