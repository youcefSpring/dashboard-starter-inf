@props(['id', 'title', 'tags' => []])

{{-- Gallery section wrapper used only by resources/views/ui-kit.blade.php. --}}
<x-card :id="$id" :title="$title" class="scroll-mt-32">
    <x-slot:actions>
        <div class="flex flex-wrap gap-1">
            @foreach ($tags as $tag)
                <code class="rounded-md bg-surface-2 px-1.5 py-0.5 font-mono text-[11px] text-fg-2 ring-1 ring-inset ring-line">&lt;x-{{ $tag }}&gt;</code>
            @endforeach
        </div>
    </x-slot:actions>
    <div class="space-y-6">{{ $slot }}</div>
</x-card>
