@extends('layouts.admin')

@section('title', 'Edit Supplier')

@section('header', 'Edit Supplier')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="p-6">
            <form action="{{ route('suppliers.update', $supplier) }}" method="POST" x-data="{ selectedMaterials: @json($supplier->materials->pluck('id')->toArray()) }">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Supplier Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Supplier Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $supplier->name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('name') border-red-500 @enderror"
                               placeholder="Enter supplier name"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div class="md:col-span-2">
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', $supplier->phone) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('phone') border-red-500 @enderror"
                               placeholder="Enter phone number (e.g., 081234567890)"
                               required>
                        <p class="mt-1 text-xs text-gray-500">Format: 081234567890 (without spaces or dashes)</p>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Materials Selection -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Assign Materials
                        </label>
                        
                        @if($allMaterials->isNotEmpty())
                            <div class="max-h-64 overflow-y-auto border border-gray-300 rounded-md p-4 dark:border-gray-600 dark:bg-gray-700">
                                @foreach($allMaterials as $material)
                                    @php
                                        $isCurrentlyAssigned = $supplier->materials->contains($material->id);
                                        $isAssignedToOther = $material->supplier_id && $material->supplier_id !== $supplier->id;
                                        $isSelectable = $isCurrentlyAssigned || !$material->supplier_id;
                                    @endphp
                                    
                                    <label class="flex items-center space-x-3 mb-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 p-2 rounded {{ $isAssignedToOther ? 'opacity-50' : '' }}">
                                        <input type="checkbox" 
                                               name="materials[]" 
                                               value="{{ $material->id }}"
                                               class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                                               x-model="selectedMaterials"
                                               {{ $isCurrentlyAssigned ? 'checked' : '' }}
                                               {{ !$isSelectable ? 'disabled' : '' }}
                                               {{ in_array($material->id, old('materials', $supplier->materials->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $material->name }}
                                                @if($isAssignedToOther)
                                                    <span class="text-xs text-red-500">(Assigned to {{ $material->supplier->name }})</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                Unit: {{ $material->unit }} | Price: @currency($material->price)
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            
                            <!-- Selected count -->
                            <div x-show="selectedMaterials.length > 0" class="mt-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    Selected <span x-text="selectedMaterials.length"></span> material(s)
                                </span>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic">No materials available.</p>
                        @endif
                        
                        @error('materials')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('suppliers.index') }}" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Update Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection