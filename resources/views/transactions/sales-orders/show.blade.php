@extends('layouts.admin')

@section('title', 'Sales Order Details')

@section('content')
<div class="space-y-8">
    <!-- Enhanced Header Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('sales-orders.index') }}" 
                   class="text-gray-400 hover:text-gray-600 mr-6 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Sales Order Details</h1>
                    <p class="mt-2 text-base text-gray-600">Order #{{ $order->order_number }}</p>
                </div>
            </div>
            
            <div class="flex items-center gap-x-3">
                <!-- Status Badge -->
                @php
                    $statusConfig = [
                        'pending' => [
                            'class' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                        ],
                        'completed' => [
                            'class' => 'bg-green-100 text-green-800 border border-green-200',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                        ],
                        'cancelled' => [
                            'class' => 'bg-red-100 text-red-800 border border-red-200',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                        ]
                    ];
                    $currentStatus = $statusConfig[$order->status] ?? $statusConfig['pending'];
                @endphp
                <span class="inline-flex items-center gap-x-1.5 rounded-lg px-3 py-1.5 text-sm font-medium {{ $currentStatus['class'] }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $currentStatus['icon'] !!}
                    </svg>
                    {{ ucfirst($order->status) }}
                </span>

                <!-- Action Buttons -->
                @if($order->status === 'pending')
                    <a href="{{ route('sales-orders.edit', $order) }}" 
                       class="inline-flex items-center gap-x-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-500 transition-colors duration-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Order
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Summary Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-8 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Order Information</h2>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-4">Order Details</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Order Number</dt>
                                    <dd class="text-sm text-gray-900 font-mono">{{ $order->order_number }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Order Date</dt>
                                    <dd class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($order->dt)->format('d F Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd>
                                        <span class="inline-flex items-center gap-x-1.5 rounded-md px-2 py-1 text-xs font-medium {{ $currentStatus['class'] }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                {!! $currentStatus['icon'] !!}
                                            </svg>
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                                    <dd class="text-sm text-gray-900">{{ $order->created_at->format('d F Y, H:i') }}</dd>
                                </div>
                                @if($order->updated_at != $order->created_at)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                    <dd class="text-sm text-gray-900">{{ $order->updated_at->format('d F Y, H:i') }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-4">Customer Information</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-sm font-semibold text-gray-900">{{ $order->customer->name }}</h4>
                                        @if($order->customer->email)
                                            <p class="text-sm text-gray-600">{{ $order->customer->email }}</p>
                                        @endif
                                        @if($order->customer->phone)
                                            <p class="text-sm text-gray-600">{{ $order->customer->phone }}</p>
                                        @endif
                                        @if($order->customer->address)
                                            <p class="text-sm text-gray-600 mt-1">{{ $order->customer->address }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="space-y-6">
            @if($order->status === 'pending')
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <button onclick="completeOrder()" 
                                class="w-full inline-flex items-center justify-center gap-x-2 rounded-lg bg-green-600 px-4 py-3 text-sm font-semibold text-white hover:bg-green-500 transition-colors duration-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Complete Order
                        </button>
                        
                        <a href="{{ route('sales-orders.edit', $order) }}" 
                           class="w-full inline-flex items-center justify-center gap-x-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Order
                        </a>
                    </div>
                </div>
            @endif

            <!-- Order Summary Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Order Summary</h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div class="flex items-center justify-between">
                            <dt class="text-sm font-medium text-gray-500">Total Items</dt>
                            <dd class="text-sm font-semibold text-gray-900">{{ $order->orderDetails->count() }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-sm font-medium text-gray-500">Total Quantity</dt>
                            <dd class="text-sm font-semibold text-gray-900">{{ number_format($order->orderDetails->sum('qty'), 2) }}</dd>
                        </div>
                        <div class="pt-3 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <dt class="text-base font-semibold text-gray-900">Order Status</dt>
                                <dd>
                                    <span class="inline-flex items-center gap-x-1.5 rounded-md px-2 py-1 text-xs font-medium {{ $currentStatus['class'] }}">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            {!! $currentStatus['icon'] !!}
                                        </svg>
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </dd>
                            </div>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Details Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gray-50 px-8 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Order Items</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-8 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                            Product
                        </th>
                        <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                            SKU
                        </th>
                        <th class="px-3 py-4 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">
                            Quantity
                        </th>
                        <th class="px-3 py-4 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">
                            Unit
                        </th>
                        <th class="px-3 py-4 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">
                            Stock After Order
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($order->orderDetails as $detail)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-8 py-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-medium text-gray-900">{{ $detail->product->name }}</div>
                                        @if($detail->product->description)
                                            <div class="text-sm text-gray-500">{{ Str::limit($detail->product->description, 50) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-6">
                                <span class="font-mono text-sm text-gray-900">{{ $detail->product->sku }}</span>
                            </td>
                            <td class="px-3 py-6 text-center">
                                <span class="inline-flex items-center rounded-full bg-primary-100 px-2.5 py-0.5 text-xs font-medium text-primary-800">
                                    {{ number_format($detail->qty, 2) }}
                                </span>
                            </td>
                            <td class="px-3 py-6 text-center">
                                <span class="text-sm text-gray-600">{{ $detail->product->unit ?? 'pcs' }}</span>
                            </td>
                            <td class="px-3 py-6 text-center">
                                @php
                                    $currentStock = $detail->product->qty;
                                    $stockAfterOrder = $order->status === 'completed' ? $currentStock : ($currentStock - $detail->qty);
                                @endphp
                                @if($stockAfterOrder > 10)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                        {{ number_format($stockAfterOrder, 2) }}
                                    </span>
                                @elseif($stockAfterOrder > 0)
                                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">
                                        {{ number_format($stockAfterOrder, 2) }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                        Out of Stock
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8l-1 1m-6 6l-1 1"/>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No items</h3>
                                    <p class="mt-1 text-sm text-gray-500">This order has no items.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function completeOrder() {
    Swal.fire({
        title: 'Complete Sales Order',
        html: `
            <div class="text-left">
                <p class="text-gray-600 mb-4">Are you sure you want to complete this sales order?</p>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Important Notice</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>Completing this order will:</p>
                                <ul class="list-disc list-inside mt-1 space-y-1">
                                    <li>Reduce product stock quantities</li>
                                    <li>Change order status to "Completed"</li>
                                    <li>Make the order non-editable</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Complete Order',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Processing...',
                text: 'Completing sales order',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Submit completion request
            fetch(`{{ route('sales-orders.complete', $order) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success) {
                    Swal.fire({
                        title: 'Order Completed!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#059669'
                    }).then(() => {
                        // Reload page to show updated status
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Failed to complete order',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                
                Swal.fire({
                    title: 'Error',
                    text: 'An unexpected error occurred. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            });
        }
    });
}
</script>
@endpush

@push('styles')
<link href="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
@endpush