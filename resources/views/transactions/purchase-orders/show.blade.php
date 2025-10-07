@extends('layouts.admin')

@section('title', 'Purchase Order Details')

@section('content')
<div class="space-y-8">
    <!-- Enhanced Header Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('purchase-orders.index') }}" class="text-gray-400 hover:text-gray-600 mr-6 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Purchase Order Details</h1>
                    <p class="mt-2 text-base text-gray-600">{{ $purchaseOrder->no }}</p>
                </div>
            </div>
            <div class="flex items-center gap-x-3">
                @if($purchaseOrder->canBeModified())
                    <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" 
                       class="inline-flex items-center gap-x-2 rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-500 transition-colors duration-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Purchase Order Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Purchase Order Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">PO Number</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $purchaseOrder->no }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->dt->format('d M Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Supplier</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->supplier ? $purchaseOrder->supplier->name : '-' }}</dd>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">{!! $purchaseOrder->status_badge !!}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total</dt>
                                <dd class="mt-1 text-2xl font-bold text-primary-600">{{ $purchaseOrder->total_formatted }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->created_at->format('d M Y H:i') }}</dd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Materials Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Materials</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">#</th>
                                <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Material</th>
                                <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Unit</th>
                                <th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Quantity</th>
                                <th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Price</th>
                                <th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($purchaseOrder->purchaseOrderDetails as $index => $detail)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-3 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $detail->material ? $detail->material->name : '-' }}</div>
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-600">{{ $detail->material ? $detail->material->unit : '-' }}</td>
                                    <td class="px-3 py-4 text-right text-sm text-gray-900">{{ number_format($detail->qty, 2) }}</td>
                                    <td class="px-3 py-4 text-right text-sm text-gray-900">{{ $detail->price_formatted }}</td>
                                    <td class="px-3 py-4 text-right text-sm font-medium text-gray-900">{{ $detail->subtotal_formatted }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                            </svg>
                                            <h3 class="mt-2 text-sm font-medium text-gray-900">No materials</h3>
                                            <p class="mt-1 text-sm text-gray-500">No materials found for this purchase order.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($purchaseOrder->purchaseOrderDetails->count() > 0)
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-right text-sm font-semibold text-gray-900">Total:</td>
                                    <td class="px-3 py-4 text-right text-lg font-bold text-primary-600">{{ $purchaseOrder->total_formatted }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            @if($purchaseOrder->canBeModified())
                <!-- Actions Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-green-50 px-6 py-4 border-b border-green-200">
                        <h3 class="text-lg font-semibold text-green-900">Actions</h3>
                    </div>
                    <div class="p-6">
                        <button class="complete-btn w-full inline-flex items-center justify-center gap-x-2 rounded-lg bg-green-600 px-4 py-3 text-sm font-semibold text-white hover:bg-green-500 transition-colors duration-200" 
                                data-url="{{ route('purchase-orders.complete', $purchaseOrder) }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Complete Purchase Order
                        </button>
                        <p class="mt-3 text-xs text-gray-600">
                            Completing the purchase order will update material stock levels and cannot be undone.
                        </p>
                    </div>
                </div>
            @endif

            @if($purchaseOrder->supplier)
                <!-- Supplier Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-blue-50 px-6 py-4 border-b border-blue-200">
                        <h3 class="text-lg font-semibold text-blue-900">Supplier Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->supplier->name }}</dd>
                            </div>
                            @if($purchaseOrder->supplier->phone)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->supplier->phone }}</dd>
                                </div>
                            @endif
                            @if($purchaseOrder->supplier->address)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Address</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->supplier->address }}</dd>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Complete handler
    $('.complete-btn').on('click', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        
        Swal.fire({
            title: 'Complete Purchase Order?',
            text: "This will update material stock and cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, complete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Completed!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        var response = xhr.responseJSON;
                        Swal.fire('Error!', response.message || 'Something went wrong!', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush