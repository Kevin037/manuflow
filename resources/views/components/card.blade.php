@props([
    'title' => null,
    'subtitle' => null,
    'actions' => null,
    'padding' => 'p-6'
])

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-lg bg-white shadow']) }}>
    @if($title || $subtitle || $actions)
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    @if($title)
                        <h3 class="text-lg font-medium leading-6 text-gray-900">{{ $title }}</h3>
                    @endif
                    @if($subtitle)
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">{{ $subtitle }}</p>
                    @endif
                </div>
                @if($actions)
                    <div class="flex space-x-3">
                        {{ $actions }}
                    </div>
                @endif
            </div>
        </div>
    @endif
    
    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</div>