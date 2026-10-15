@props(['index'])

<div role="tabpanel" x-show="isActive({{ $index }})" @if ($index > 0) x-cloak @endif class="[&_pre]:overflow-x-auto [&_pre]:px-7 [&_pre]:py-6 [&_pre]:font-mono [&_pre]:text-[14.5px] [&_pre]:leading-[1.75]">{{ $slot }}</div>
