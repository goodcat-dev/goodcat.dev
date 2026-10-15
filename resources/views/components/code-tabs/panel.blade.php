@props(['index'])

<pre role="tabpanel" x-show="isActive({{ $index }})" @if ($index > 0) x-cloak @endif class="overflow-x-auto px-7 py-6 font-mono text-[14.5px] leading-[1.75] text-zinc-200">{{ $slot }}</pre>
