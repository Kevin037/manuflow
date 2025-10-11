@extends('layouts.admin')

@section('title', 'Dashboard')

@section('header', 'Dashboard')

@section('description', 'Welcome to Manuflow - Your Manufacturing Control Center')

@section('content')
<!-- Stats Grid -->
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <!-- Total Products Card -->
    <div class="relative overflow-hidden rounded-xl bg-white px-4 py-5 shadow-md sm:p-6 border border-gray-100 hover:shadow-lg transition-shadow duration-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary-100">
                    <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4 8-4M4 7v10l8 4 8-4V7"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4 flex-1">
                <dt class="text-sm font-medium text-gray-500">Total Products</dt>
                <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">{{ \App\Models\Product::count() ?? 0 }}</div>
                    <div class="ml-2 flex items-baseline text-sm">
                        <div class="text-green-600">+2.5%</div>
                        <div class="ml-1 text-gray-500">vs last month</div>
                    </div>
                </dd>
            </div>
        </div>
    </div>

    <!-- Total Materials Card -->
    <div class="relative overflow-hidden rounded-xl bg-white px-4 py-5 shadow-md sm:p-6 border border-gray-100 hover:shadow-lg transition-shadow duration-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4 flex-1">
                <dt class="text-sm font-medium text-gray-500">Total Materials</dt>
                <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">{{ \App\Models\Material::count() ?? 0 }}</div>
                    <div class="ml-2 flex items-baseline text-sm">
                        <div class="text-green-600">+5.1%</div>
                        <div class="ml-1 text-gray-500">vs last month</div>
                    </div>
                </dd>
            </div>
        </div>
    </div>

    <!-- Pending Orders Card -->
    <div class="relative overflow-hidden rounded-xl bg-white px-4 py-5 shadow-md sm:p-6 border border-gray-100 hover:shadow-lg transition-shadow duration-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-100">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4 flex-1">
                <dt class="text-sm font-medium text-gray-500">Pending Orders</dt>
                <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">{{ \App\Models\Order::where('status', 'pending')->count() ?? 0 }}</div>
                    <div class="ml-2 flex items-baseline text-sm">
                        <div class="text-red-600">-3.2%</div>
                        <div class="ml-1 text-gray-500">vs last month</div>
                    </div>
                </dd>
            </div>
        </div>
    </div>

    <!-- Active Productions Card -->
    <div class="relative overflow-hidden rounded-xl bg-white px-4 py-5 shadow-md sm:p-6 border border-gray-100 hover:shadow-lg transition-shadow duration-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <div class="ml-4 flex-1">
                <dt class="text-sm font-medium text-gray-500">Active Productions</dt>
                <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">{{ \App\Models\Production::where('status', 'pending')->count() ?? 0 }}</div>
                    <div class="ml-2 flex items-baseline text-sm">
                        <div class="text-blue-600">+8.7%</div>
                        <div class="ml-1 text-gray-500">vs last month</div>
                    </div>
                </dd>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Growth Chart -->
<div class="mt-8">
    @include('dashboard._monthly_summary_cards')
        @include('dashboard._monthly_growth')
        <div class="h-72"></div>
        <!-- Spacer to give chart some height for maintainAspectRatio false -->
    </div>

<!-- Top Products: Monthly Revenue + Drilldown -->
<div class="mt-8">
    @include('dashboard._monthly_top_products')
    <div class="h-72"></div>
</div>

<!-- Content Grid -->
{{-- <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
    <!-- Recent Orders -->
    <x-card title="Recent Orders" subtitle="Latest customer orders in the system" class="shadow-md">
        <x-slot name="actions">
            <button type="button" class="inline-flex items-center rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 transition-colors duration-200">
                <svg class="-ml-0.5 mr-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
                </svg>
                New Order
            </button>
        </x-slot>
        
        <div class="flow-root">
            <div class="divide-y divide-gray-200">
                <div class="py-4 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No orders yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating your first order.</p>
                    <div class="mt-4">
                        <button type="button" class="inline-flex items-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
                            Create Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </x-card>

    <!-- Recent Productions -->
    <x-card title="Recent Productions" subtitle="Latest production activities and status" class="shadow-md">
        <x-slot name="actions">
            <button type="button" class="inline-flex items-center rounded-lg bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 transition-colors duration-200">
                <svg class="-ml-0.5 mr-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
                </svg>
                Start Production
            </button>
        </x-slot>
        
        <div class="flow-root">
            <div class="divide-y divide-gray-200">
                <div class="py-4 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No productions yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Start your first production run to see activity here.</p>
                    <div class="mt-4">
                        <button type="button" class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                            Start Production
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </x-card>
</div> --}}

<!-- Analytics Section -->
{{-- <x-card title="Production Overview" subtitle="Monthly production and sales analytics" class="shadow-md">
    <x-slot name="actions">
        <div class="flex items-center space-x-2">
            <select class="rounded-md border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                <option>Last 7 days</option>
                <option>Last 30 days</option>
                <option>Last 90 days</option>
                <option>Last year</option>
            </select>
            <button type="button" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                <svg class="-ml-0.5 mr-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                Export
            </button>
        </div>
    </x-slot>
    
    <div class="h-80 flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-dashed border-gray-300">
        <div class="text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Analytics Dashboard</h3>
            <p class="mt-2 text-sm text-gray-500">Production charts and analytics will be displayed here.<br>Start creating data to see beautiful visualizations.</p>
            <div class="mt-6">
                <button type="button" class="inline-flex items-center rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-500 transition-colors duration-200">
                    <svg class="-ml-1 mr-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
                    </svg>
                    Setup Demo Data
                </button>
            </div>
        </div>
    </div>
</x-card> --}}

<!-- Quick Actions -->
<div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="relative group">
        <a href="{{ route('products.create') }}" class="block rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 p-6 text-white shadow-md hover:shadow-lg transition-all duration-200 transform group-hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" aria-label="Add Product">
            <div class="flex items-center">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4 8-4M4 7v10l8 4 8-4V7"/>
                </svg>
                <div class="ml-3">
                    <h3 class="text-lg font-medium">Add Product</h3>
                    <p class="text-primary-100">Create new product</p>
                </div>
            </div>
        </a>
    </div>
    
    <div class="relative group">
        <a href="{{ route('materials.create') }}" class="block rounded-xl bg-gradient-to-r from-green-500 to-green-600 p-6 text-white shadow-md hover:shadow-lg transition-all duration-200 transform group-hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" aria-label="Add Material">
            <div class="flex items-center">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <div class="ml-3">
                    <h3 class="text-lg font-medium">Add Material</h3>
                    <p class="text-green-100">Register new material</p>
                </div>
            </div>
        </a>
    </div>
    
    <div class="relative group">
        <a href="{{ route('purchase-orders.create') }}" class="block rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 p-6 text-white shadow-md hover:shadow-lg transition-all duration-200 transform group-hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500" aria-label="Create Purchase Order">
            <div class="flex items-center">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <div class="ml-3">
                    <h3 class="text-lg font-medium">Purchase Order</h3>
                    <p class="text-purple-100">Create Purchase Order</p>
                </div>
            </div>
        </a>
    </div>

    <div class="relative group">
        <a href="{{ route('sales-orders.create') }}" class="block rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-white shadow-md hover:shadow-lg transition-all duration-200 transform group-hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" aria-label="Create Sales Order">
            <div class="flex items-center">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <div class="ml-3">
                    <h3 class="text-lg font-medium">New Order</h3>
                    <p class="text-blue-100">Create Order</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Add any dashboard-specific interactions here
    console.log('Dashboard loaded successfully');
});
</script>
@endpush
