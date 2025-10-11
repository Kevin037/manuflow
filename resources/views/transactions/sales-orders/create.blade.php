@extends('layouts.admin')

@section('title', 'Create Sales Order')

@section('content')
<div class="space-y-8">
    <!-- Enhanced Header Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="flex items-center">
            <a href="{{ route('sales-orders.index') }}" class="text-gray-400 hover:text-gray-600 mr-6 transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create Sales Order</h1>
                <p class="mt-2 text-base text-gray-600">Create a new sales order with real-time stock validation</p>
            </div>
        </div>
    </div>

    <!-- Enhanced Form Card -->
    <form method="POST" action="{{ route('sales-orders.store') }}" id="salesOrderForm">
        @csrf
        
        <!-- Basic Information Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="bg-gray-50 px-8 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Sales Order Information</h2>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <label for="dt" class="block text-sm font-semibold text-gray-700 mb-2">
                            Sales Order Date *
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
                        <label for="customer_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Customer *
                        </label>
                        <select name="customer_id" id="customer_id" 
                                class="select2 w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 @error('customer_id') border-red-500 ring-2 ring-red-200 @enderror">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="bg-gray-50 px-8 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Products</h2>
                <button type="button" 
                        class="inline-flex items-center gap-x-2 rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white hover:bg-primary-500 transition-colors duration-200" 
                        id="addProductBtn">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Product
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200" id="productsTable">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                Product
                            </th>
                            <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                Current Stock
                            </th>
                            <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                Unit
                            </th>
                            <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                Quantity
                            </th>
                            <th class="px-3 py-4 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody" class="divide-y divide-gray-200">
                        <!-- Dynamic rows will be added here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-x-4">
            <a href="{{ route('sales-orders.index') }}" 
               class="inline-flex items-center gap-x-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600 transition-all duration-200">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex items-center gap-x-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-all duration-200">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Save Sales Order
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productsData = @json($products);
    let rowIndex = 0;

    // Add initial row
    addProductRow();

    // Add product row button
    document.getElementById('addProductBtn').addEventListener('click', function() {
        addProductRow();
    });
// tesselect2
    function addProductRow() {
        const tbody = document.getElementById('productsTableBody');
        const currentIndex = rowIndex; // Capture current index before increment
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 transition-colors duration-200';
        row.innerHTML = `
            <td class="px-6 py-4">
                <select class="select2 product-select w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200" style="width: 100%;" 
                        name="products[${currentIndex}][product_id]" required>
                    <option value="">Select Product</option>
                    ${productsData.map(product => 
                        `<option value="${product.id}" data-stock="${product.qty}" data-unit="${product.unit || 'pcs'}">${product.name} (${product.sku})</option>`
                    ).join('')}
                </select>
            </td>
            <td class="px-3 py-4">
                <span class="stock-display inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">-</span>
            </td>
            <td class="px-3 py-4">
                <span class="unit-display text-sm text-gray-600">-</span>
            </td>
            <td class="px-3 py-4">
                <input type="number" 
                       class="qty-input w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200" 
                       name="products[${currentIndex}][qty]" 
                       step="0.01" min="0.01" placeholder="0.00" required
                       data-stock="0">
            </td>
            <td class="px-3 py-4 text-center">
                <button type="button" 
                        class="inline-flex items-center rounded-lg bg-red-50 p-1.5 text-red-700 hover:bg-red-100 transition-colors duration-200 remove-row" 
                        onclick="removeProductRow(this)">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </td>
        `;
        tbody.appendChild(row);
        rowIndex++; // Increment row index for next row
// tesselect2
        // Bind events and initialize Select2 for product selection
        const productSelect = row.querySelector('.product-select');
        bindProductEvents(productSelect, row);
        initSelect2(productSelect, row);

        // Add event listener for quantity validation
        const qtyInput = row.querySelector('.qty-input');
        qtyInput.addEventListener('input', function() {
            validateQuantityRealTime(this);
        });

        qtyInput.addEventListener('blur', function() {
            validateQuantity(this);
        });

        return row;
    }

    function updateProductStock(selectElement, row) {
        const value = (selectElement && typeof selectElement.value !== 'undefined') ? selectElement.value : '';
        const stockDisplay = row.querySelector('.stock-display');
        const unitDisplay = row.querySelector('.unit-display');
        const qtyInput = row.querySelector('.qty-input');
        
        if (!value) {
            // Reset to default state when no product selected
            resetStockDisplay(stockDisplay, unitDisplay, qtyInput);
            return;
        }

        // Duplicate check using helper
        if (isProductDuplicate(value, selectElement)) {
            if (selectElement.dataset && selectElement.dataset.suppressDuplicate === '1') {
                try { delete selectElement.dataset.suppressDuplicate; } catch (e) { selectElement.dataset.suppressDuplicate = ''; }
                return;
            } else {
                Swal.fire({
                    title: 'Product Duplicate',
                    text: 'This product has already been selected in another row. Please choose a different product or update the existing quantity.',
                    icon: 'warning',
                    confirmButtonColor: '#f59e0b'
                });
                if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
                    jQuery(selectElement).val(null).trigger('change');
                } else {
                    selectElement.value = '';
                    try { selectElement.dispatchEvent(new Event('change', { bubbles: true })); } catch (e) {}
                }
                resetStockDisplay(stockDisplay, unitDisplay, qtyInput);
                return;
            }
        }

        // Find the selected option safely by value
        let selectedOption = null;
        for (let i = 0; i < selectElement.options.length; i++) {
            const opt = selectElement.options[i];
            if (opt.value === value) { selectedOption = opt; break; }
        }

        const stock = parseFloat(selectedOption && selectedOption.dataset ? selectedOption.dataset.stock : 0) || 0;
        const unit = (selectedOption && selectedOption.dataset && selectedOption.dataset.unit) ? selectedOption.dataset.unit : 'pcs';
        
        // Update stock display with proper formatting and color
        updateStockDisplay(stockDisplay, stock);
        unitDisplay.textContent = unit;
        
        // Store stock data in qty input for validation
        qtyInput.setAttribute('data-stock', stock);
        
        // Auto-focus quantity input for better UX
        setTimeout(() => qtyInput.focus(), 100);
    }

    function isProductDuplicate(value, currentSelect) {
        if (!value) return false;
        let dup = false;
        document.querySelectorAll('.product-select').forEach(sel => {
            if (sel !== currentSelect && String(sel.value) === String(value)) {
                dup = true;
            }
        });
        return dup;
    }

    function bindProductEvents(selectEl, row) {
        // Native change updates stock/unit
        selectEl.addEventListener('change', function() {
            updateProductStock(this, row);
        });

        // Select2-specific event hooks
        if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
            const $el = jQuery(selectEl);
            $el.off('select2:opening.so select2:select.so select2:clear.so');
            $el.on('select2:opening.so', function() { $el.data('prev', $el.val()); });
            $el.on('select2:select.so', function(e) {
                const selectedId = e && e.params && e.params.data ? String(e.params.data.id) : String($el.val() || '');
                if (isProductDuplicate(selectedId, selectEl)) {
                    selectEl.dataset.suppressDuplicate = '1';
                    Swal.fire({
                        title: 'Product Duplicate',
                        text: 'This product has already been selected in another row. Please choose a different product or update the existing quantity.',
                        icon: 'warning',
                        confirmButtonColor: '#f59e0b'
                    }).then(() => {
                        const prev = $el.data('prev') || null;
                        $el.val(prev).trigger('change');
                        setTimeout(() => { try { $el.select2('open'); } catch (err) {} }, 0);
                    });
                } else {
                    updateProductStock(selectEl, row);
                }
            });
            $el.on('select2:clear.so', function() {
                resetStockDisplay(row.querySelector('.stock-display'), row.querySelector('.unit-display'), row.querySelector('.qty-input'));
            });
        }
    }

    function initSelect2(selectEl, row) {
        try {
            if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
                const $el = jQuery(selectEl);
                if (!$el.hasClass('select2-hidden-accessible') && !$el.data('select2')) {
                    const dpEl = (row && row.closest && row.closest('.modal .modal-content')) || document.querySelector('.modal.show .modal-content') || document.body;
                    $el.select2({ theme: 'bootstrap-5', width: 'style', dropdownParent: jQuery(dpEl) });
                } else {
                    $el.trigger('change.select2');
                }
            } else {
                document.dispatchEvent(new Event('reinit-select2'));
                setTimeout(() => { try { initSelect2(selectEl, row); } catch (e) {} }, 120);
            }
        } catch (e) { /* no-op */ }
    }

    function updateStockDisplay(stockDisplay, stock) {
        stockDisplay.textContent = stock.toLocaleString('id-ID');
        
        // Update stock badge color based on availability
        if (stock > 10) {
            stockDisplay.className = 'stock-display inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800';
        } else if (stock > 0) {
            stockDisplay.className = 'stock-display inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800';
        } else {
            stockDisplay.className = 'stock-display inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-100 text-red-800';
            stockDisplay.textContent = 'Out of Stock';
        }
    }

    function resetStockDisplay(stockDisplay, unitDisplay, qtyInput) {
        stockDisplay.textContent = '-';
        stockDisplay.className = 'stock-display inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800';
        unitDisplay.textContent = '-';
        qtyInput.setAttribute('data-stock', '0');
        qtyInput.value = '';
    }

    function validateQuantityRealTime(input) {
        const qty = parseFloat(input.value);
        const stock = parseFloat(input.getAttribute('data-stock')) || 0;
        
        if (qty && stock > 0 && qty > stock) {
            // Add visual feedback for insufficient stock
            input.classList.add('border-red-500', 'ring-2', 'ring-red-200');
            input.classList.remove('border-gray-300');
            
            // Show tooltip or add warning text
            if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('stock-warning')) {
                const warning = document.createElement('p');
                warning.className = 'stock-warning mt-1 text-xs text-red-600 flex items-center';
                warning.innerHTML = `
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Insufficient stock! Available: ${stock.toLocaleString('id-ID')}
                `;
                input.parentNode.appendChild(warning);
            }
        } else {
            // Remove visual feedback when valid
            input.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
            input.classList.add('border-gray-300');
            
            // Remove warning text
            const warning = input.parentNode.querySelector('.stock-warning');
            if (warning) {
                warning.remove();
            }
        }
    }

    function validateQuantity(input) {
        const qty = parseFloat(input.value);
        const stock = parseFloat(input.getAttribute('data-stock')) || 0;
        
        if (qty && stock > 0 && qty > stock) {
            Swal.fire({
                title: 'Insufficient Stock!',
                html: `
                    <div class="text-left">
                        <p class="text-gray-600 mb-2">The requested quantity exceeds available stock:</p>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p><strong>Requested:</strong> ${qty.toLocaleString('id-ID')}</p>
                            <p><strong>Available:</strong> ${stock.toLocaleString('id-ID')}</p>
                            <p class="text-red-600"><strong>Shortage:</strong> ${(qty - stock).toLocaleString('id-ID')}</p>
                        </div>
                    </div>
                `,
                icon: 'error',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'I Understand'
            }).then(() => {
                input.focus();
                input.select();
            });
        }
    }

    window.removeProductRow = function(button) {
        const row = button.closest('tr');
        const tbody = document.getElementById('productsTableBody');
        
        // Keep at least one row
        if (tbody.children.length > 1) {
            row.remove();
        } else {
            // Clear the last row instead of removing it
            const clearSelect = row.querySelector('.product-select');
            if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
                jQuery(clearSelect).val(null).trigger('change');
            } else {
                clearSelect.value = '';
                try { clearSelect.dispatchEvent(new Event('change', { bubbles: true })); } catch (e) {}
            }
            row.querySelector('.qty-input').value = '';
            const stockDisplay = row.querySelector('.stock-display');
            const unitDisplay = row.querySelector('.unit-display');
            const qtyInput = row.querySelector('.qty-input');
            resetStockDisplay(stockDisplay, unitDisplay, qtyInput);
        }
    };

    // Form validation
    document.getElementById('salesOrderForm').addEventListener('submit', function(e) {
        const productSelects = document.querySelectorAll('.product-select');
        const qtyInputs = document.querySelectorAll('.qty-input');
        let hasValidProduct = false;
        let hasStockIssue = false;

        productSelects.forEach((select, index) => {
            if (select.value) {
                hasValidProduct = true;
                
                // Check stock validation for this row
                const qtyInput = qtyInputs[index];
                const qty = parseFloat(qtyInput.value);
                const stock = parseFloat(qtyInput.getAttribute('data-stock')) || 0;
                
                if (qty && stock > 0 && qty > stock) {
                    hasStockIssue = true;
                }
            }
        });

        if (!hasValidProduct) {
            e.preventDefault();
            
            // Show SweetAlert2 error
            Swal.fire({
                title: 'Validation Error',
                text: 'Please select at least one product.',
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
            return false;
        }

        if (hasStockIssue) {
            e.preventDefault();
            
            // Show SweetAlert2 error for stock issues
            Swal.fire({
                title: 'Stock Validation Error',
                text: 'Some products have insufficient stock. Please adjust quantities or remove items with stock issues.',
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
            return false;
        }
    });
    // Initialize customer select explicitly
    const customerSelect = document.getElementById('customer_id');
    if (customerSelect) initSelect2(customerSelect);

    // Initialize any existing product selects (if any pre-rendered in future)
    document.querySelectorAll('.product-select').forEach(sel => initSelect2(sel));

    // Global reinit hook
    document.addEventListener('reinit-select2', function() {
        document.querySelectorAll('select.select2').forEach(sel => initSelect2(sel));
    });

    // On full window load, ensure Select2 widths are correct
    window.addEventListener('load', function() {
        try {
            if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
                document.querySelectorAll('.product-select').forEach(function(sel) {
                    const $el = jQuery(sel);
                    if ($el.hasClass('select2-hidden-accessible')) {
                        const val = $el.val();
                        $el.select2('destroy');
                        initSelect2(sel);
                        if (val) { $el.val(val).trigger('change'); }
                    } else {
                        initSelect2(sel);
                    }
                });
            }
        } catch (e) { /* no-op */ }
    });

    // Final nudge to (re)initialize all Select2s in case of late script loading
    try { document.dispatchEvent(new Event('reinit-select2')); } catch (e) {}
});
</script>
@endpush

@push('styles')
<link href="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
@endpush