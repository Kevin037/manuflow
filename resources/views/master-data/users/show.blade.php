@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('users.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">User Details</h1>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('users.edit', $user) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                    Edit User
                </a>
                <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg">
                        Delete User
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
                    <p class="text-gray-900 text-lg">{{ $user->name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <p class="text-gray-900 text-lg">{{ $user->email }}</p>
                </div>

                @if($user->photo)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Photo</label>
                    <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="w-32 h-32 rounded-full object-cover">
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Created At</label>
                    <p class="text-gray-900">{{ $user->created_at->format('d M Y, H:i') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Updated</label>
                    <p class="text-gray-900">{{ $user->updated_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection