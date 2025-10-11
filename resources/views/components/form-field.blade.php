@props([
    'label' => null,
    'name' => '',
    'type' => 'text',
    'value' => null,
    'placeholder' => '',
    'required' => false,
    'readonly' => false,
    'disabled' => false,
    'help' => null,
    'error' => null
])

<div class="space-y-1">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="relative">
        @if($type === 'textarea')
            <textarea 
                id="{{ $name }}" 
                name="{{ $name }}"
                rows="3"
                placeholder="{{ $placeholder }}"
                @if($required) required @endif
                @if($readonly) readonly @endif
                @if($disabled) disabled @endif
                {{ $attributes->merge(['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm' . ($error ? ' border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500' : '')]) }}
            >{{ old($name, $value) }}</textarea>
        @elseif($type === 'select')
            <select 
                id="{{ $name }}" 
                name="{{ $name }}"
                @if($required) required @endif
                @if($disabled) disabled @endif
                {{ $attributes->merge(['class' => 'select2 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm' . ($error ? ' border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500' : '')]) }}
            >
                {{ $slot }}
            </select>
        @else
            <input 
                type="{{ $type }}" 
                id="{{ $name }}" 
                name="{{ $name }}"
                value="{{ old($name, $value) }}"
                placeholder="{{ $placeholder }}"
                @if($required) required @endif
                @if($readonly) readonly @endif
                @if($disabled) disabled @endif
                {{ $attributes->merge(['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm' . ($error ? ' border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500' : '')]) }}
            />
        @endif
    </div>
    
    @if($error)
        <p class="text-sm text-red-600">{{ $error }}</p>
    @elseif($help)
        <p class="text-sm text-gray-500">{{ $help }}</p>
    @endif
</div>