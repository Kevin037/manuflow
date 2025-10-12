@extends('layouts.admin')

@section('title', 'Create Supplier')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <div class="flex items-center">
            <a href="{{ route('suppliers.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Create Supplier</h1>
        </div>
        <p class="mt-1 text-sm text-gray-600">Create a new supplier record</p>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <form action="{{ route('suppliers.store') }}" method="POST" x-data="{ selectedMaterials: [] }">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Supplier Name <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('name') border-red-500 @enderror" 
                               placeholder="Enter supplier name" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number <span class="text-red-500">*</span></label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('phone') border-red-500 @enderror" 
                               placeholder="Enter phone number (e.g., 081234567890)" required>
                        <p class="mt-1 text-xs text-gray-500">Format: 081234567890 (without spaces or dashes)</p>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign Materials</label>
                        @if($availableMaterials->isNotEmpty())
                            <div class="max-h-64 overflow-y-auto border border-gray-300 rounded-lg p-4">
                                @foreach($availableMaterials as $material)
                                    <label class="flex items-center space-x-3 mb-3 cursor-pointer hover:bg-gray-50 p-2 rounded">
                                        <input type="checkbox" name="materials[]" value="{{ $material->id }}" 
                                               class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500" 
                                               x-model="selectedMaterials"
                                               {{ in_array($material->id, old('materials', [])) ? 'checked' : '' }}>
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-gray-900">{{ $material->name }}</div>
                                            <div class="text-xs text-gray-500">Unit: {{ $material->unit }} | Price: @currency($material->price)</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <div x-show="selectedMaterials.length > 0" class="mt-2">
                                <span class="text-sm text-gray-600">Selected <span x-text="selectedMaterials.length"></span> material(s)</span>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic">No materials available for assignment. All materials are already assigned to suppliers.</p>
                        @endif
                        @error('materials')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <a href="{{ route('suppliers.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg">Cancel</a>
                    <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded-lg">Create Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection