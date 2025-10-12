@extends('layouts.admin')

@section('title', 'Profile')

@section('content')
<div class="space-y-8">
    <!-- Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Profile Settings</h1>
                <p class="mt-2 text-sm text-gray-600">Manage your personal information, password, and account</p>
            </div>
        </div>
    </div>

    <!-- Profile Information -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-900">Profile Information</h2>
            <p class="mt-1 text-sm text-gray-600">Update your account's profile information and email address</p>
        </div>
        <div class="p-6">
            <div class="max-w-2xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>
    </div>

    <!-- Update Password -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-900">Update Password</h2>
            <p class="mt-1 text-sm text-gray-600">Ensure your account is using a long, random password</p>
        </div>
        <div class="p-6">
            <div class="max-w-2xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>

    <!-- Delete Account -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-900">Delete Account</h2>
            <p class="mt-1 text-sm text-gray-600">Permanently delete your account</p>
        </div>
        <div class="p-6">
            <div class="max-w-2xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
