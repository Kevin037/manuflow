@extends('layouts.admin')

@section('title', 'Material Details')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('materials.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Material Details</h1>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('materials.edit', $material) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                    Edit Material
                </a>
                <form action="{{ route('materials.destroy', $material) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this material?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg">
                        Delete Material
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <p class="text-gray-900 text-lg">{{ $material->name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                    <p class="text-gray-900 text-lg">Rp {{ number_format($material->price, 0, ',', '.') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stock (qty)</label>
                    <p class="text-gray-900 text-lg">{{ $material->qty }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                    <p class="text-gray-900 text-lg">{{ $material->supplier ? $material->supplier->name : 'No Supplier' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Created At</label>
                    <p class="text-gray-900">{{ $material->created_at->format('d M Y, H:i') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Updated</label>
                    <p class="text-gray-900">{{ $material->updated_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection