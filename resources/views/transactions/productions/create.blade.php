@extends('layouts.admin')

@section('title', 'New Production Order')

@section('content')
<div class="space-y-8">
    <!-- Enhanced Header Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="flex items-center">
            <a href="{{ route('productions.index') }}" class="text-gray-400 hover:text-gray-600 mr-6 transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">New Production Order</h1>
                <p class="mt-2 text-base text-gray-600">Create a new production order with real-time material validation</p>
            </div>
        </div>
    </div>

    <!-- Enhanced Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-8">
            <form action="{{ route('productions.store') }}" method="POST" id="productionForm">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <div>
                            <label for="dt" class="block text-sm font-semibold text-gray-700 mb-2">
                                Production Date *
                            </label>
                            <input type="date" name="dt" id="dt" value="{{ old('dt', date('Y-m-d')) }}" 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 @error('dt') border-red-500 ring-2 ring-red-200 @enderror">
                            @error('dt')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="product_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                Product *
                            </label>
                            <select name="product_id" id="product_id" 
                                    class="select2 w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 @error('product_id') border-red-500 ring-2 ring-red-200 @enderror">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} ({{ $product->sku }})
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <div>
                            <label for="qty" class="block text-sm font-semibold text-gray-700 mb-2">
                                Production Quantity *
                            </label>
                            <input type="number" name="qty" id="qty" step="0.01" min="0.01" 
                                   value="{{ old('qty') }}" placeholder="0.00"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 @error('qty') border-red-500 ring-2 ring-red-200 @enderror">
                            @error('qty')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                                Production Notes
                            </label>
                            <textarea name="notes" id="notes" rows="4" placeholder="Optional production notes..."
                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 @error('notes') border-red-500 ring-2 ring-red-200 @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Add any special instructions or notes for this production order</p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-8 flex items-center justify-end gap-x-4">
                    <a href="{{ route('productions.index') }}" 
                       class="inline-flex items-center gap-x-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600 transition-all duration-200">
                        Cancel
                    </a>
                    <button type="submit" id="submitBtn"
                            class="inline-flex items-center gap-x-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Create Production
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Material Stock Validation Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" id="materialStockCard" style="display: none;">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-8 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Material Stock Validation
            </h3>
            <p class="mt-1 text-sm text-gray-600">Real-time checking of material availability</p>
        </div>
        <div class="p-8">
            <div id="stockInfo" class="space-y-4">
                <!-- Real-time stock information will be displayed here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_id');
    const qtyInput = document.getElementById('qty');
    const materialStockCard = document.getElementById('materialStockCard');
    const stockInfo = document.getElementById('stockInfo');
    const submitBtn = document.getElementById('submitBtn');

    let stockCheckTimeout;
    let currentStockData = null;

    function checkMaterialStock() {
        const productId = productSelect.value;
        const qty = parseFloat(qtyInput.value);

        if (!productId || !qty || qty <= 0) {
            materialStockCard.style.display = 'none';
            submitBtn.disabled = false;
            return;
        }

        // Clear previous timeout
        clearTimeout(stockCheckTimeout);

        // Add debounce to avoid too many requests
        stockCheckTimeout = setTimeout(() => {
            stockInfo.innerHTML = '<div class="flex items-center justify-center py-8"><svg class="animate-spin h-8 w-8 text-primary-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span class="ml-2 text-gray-600">Checking material availability...</span></div>';
            materialStockCard.style.display = 'block';

            fetch('{{ route('productions.check-material-stock') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    product_id: productId,
                    qty: qty
                })
            })
            .then(response => response.json())
            .then(data => {
                currentStockData = data;
                displayStockInfo(data);
            })
            .catch(error => {
                console.error('Error checking material stock:', error);
                stockInfo.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-lg p-4"><div class="text-red-800">Error checking stock availability. Please try again.</div></div>';
            });
        }, 500);
    }

    function displayStockInfo(data) {
        let html = '';
        
        // Overall status
        if (data.can_produce) {
            html += `<div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h4 class="text-green-800 font-semibold">Production Possible</h4>
                                <p class="text-green-700 text-sm mt-1">All required materials are available in sufficient quantities.</p>
                            </div>
                        </div>
                    </div>`;
            submitBtn.disabled = false;
        } else {
            html += `<div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h4 class="text-red-800 font-semibold">Insufficient Materials</h4>
                                <p class="text-red-700 text-sm mt-1">${data.message}</p>
                            </div>
                        </div>
                    </div>`;
            submitBtn.disabled = true;
        }

        // Material details table
        if (data.materials && data.materials.length > 0) {
            html += `<div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Required/Unit</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Required</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">`;

            data.materials.forEach(material => {
                const statusClass = material.sufficient ? 'text-green-800 bg-green-100' : 'text-red-800 bg-red-100';
                const statusText = material.sufficient ? 'Sufficient' : `Short ${material.shortage.toLocaleString('id-ID')} ${material.unit}`;
                
                html += `<tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">${material.name}</div>
                                <div class="text-sm text-gray-500">${material.unit}</div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">${material.required_per_product.toLocaleString('id-ID')}</td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${material.required_total.toLocaleString('id-ID')}</td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">${material.available.toLocaleString('id-ID')}</td>
                            <td class="px-3 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                                    ${statusText}
                                </span>
                            </td>
                        </tr>`;
            });

            html += `</tbody></table></div>`;
        }

        // Show maximum producible quantity if not sufficient
        if (!data.can_produce && data.max_producible > 0) {
            html += `<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <h4 class="text-yellow-800 font-medium">Alternative Suggestion</h4>
                                    <p class="text-yellow-700 text-sm">Maximum producible quantity: ${data.max_producible.toLocaleString('id-ID')} units</p>
                                </div>
                            </div>
                            <button type="button" onclick="setMaxQuantity(${data.max_producible})" 
                                    class="inline-flex items-center px-3 py-2 border border-yellow-300 shadow-sm text-sm font-medium rounded-md text-yellow-700 bg-yellow-50 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                Use Max Qty
                            </button>
                        </div>
                    </div>`;
        }

        stockInfo.innerHTML = html;
    }

    // Set maximum quantity function
    window.setMaxQuantity = function(maxQty) {
        qtyInput.value = maxQty;
        checkMaterialStock();
    };

    // Event listeners
    productSelect.addEventListener('change', checkMaterialStock);
    qtyInput.addEventListener('input', checkMaterialStock);

    // Form submission validation
    document.getElementById('productionForm').addEventListener('submit', function(e) {
        if (currentStockData && !currentStockData.can_produce) {
            e.preventDefault();
            Swal.fire({
                title: 'Insufficient Stock',
                text: 'Cannot create production with insufficient material stock.',
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
            return false;
        }
    });
});
</script>
@endpush