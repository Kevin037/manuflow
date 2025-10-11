@extends('layouts.admin')

@section('title', 'Create Purchase Order')

@section('content')
<div class="space-y-8">
    <!-- Enhanced Header Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="flex items-center">
            <a href="{{ route('purchase-orders.index') }}" class="text-gray-400 hover:text-gray-600 mr-6 transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create Purchase Order</h1>
                <p class="mt-2 text-base text-gray-600">Create a new purchase order for material procurement</p>
            </div>
        </div>
    </div>

    <!-- Enhanced Form Card -->
    <form method="POST" action="{{ route('purchase-orders.store') }}" id="purchaseOrderForm">
        @csrf
        
        <!-- Basic Information Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="bg-gray-50 px-8 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Purchase Order Information</h2>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <label for="dt" class="block text-sm font-semibold text-gray-700 mb-2">
                            Purchase Order Date *
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
                        <label for="supplier_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Supplier *
                        </label>
                        <select name="supplier_id" id="supplier_id" 
                                class="select2 w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 @error('supplier_id') border-red-500 ring-2 ring-red-200 @enderror">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
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

        <!-- Materials Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="bg-gray-50 px-8 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Materials</h2>
                <button type="button" 
                        class="inline-flex items-center gap-x-2 rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white hover:bg-primary-500 transition-colors duration-200" 
                        id="addMaterialBtn">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Material
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200" id="materialsTable">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                Material
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
                    <tbody id="materialsTableBody" class="divide-y divide-gray-200">
                        <!-- Dynamic rows will be added here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-x-4">
            <a href="{{ route('purchase-orders.index') }}" 
               class="inline-flex items-center gap-x-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600 transition-all duration-200">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex items-center gap-x-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-all duration-200">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Save Purchase Order
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const materialsData = @json($materials);
    let rowIndex = 0;

    // Add initial row
    addMaterialRow();

    // Add material row button
    document.getElementById('addMaterialBtn').addEventListener('click', function() {
        addMaterialRow();
    });

    function addMaterialRow() {
        const tbody = document.getElementById('materialsTableBody');
        const currentIndex = rowIndex; // Capture current index before increment
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 transition-colors duration-200';
        row.innerHTML = `
            <td class="px-6 py-4">
                <select class="select2 material-select w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200" style="width: 100%;"
                        name="materials[${currentIndex}][material_id]" required>
                    <option value="">Select Material</option>
                    ${materialsData.map(material => 
                        `<option value="${material.id}" data-stock="${material.qty}" data-unit="${material.unit}">${material.name}</option>`
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
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200" 
                       name="materials[${currentIndex}][qty]" 
                       step="0.01" min="0.01" placeholder="0.00" required
                       onblur="validateQuantity(this)">
            </td>
            <td class="px-3 py-4 text-center">
                <button type="button" 
                        class="inline-flex items-center rounded-lg bg-red-50 p-1.5 text-red-700 hover:bg-red-100 transition-colors duration-200 remove-row" 
                        onclick="removeMaterialRow(this)">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </td>
        `;
        tbody.appendChild(row);
        rowIndex++; // Increment row index for next row

        // Bind material events (change + Select2-specific duplicate guard)
        const materialSelect = row.querySelector('.material-select');
        bindMaterialEvents(materialSelect, row);

        // Initialize Select2 for this material select (safe if already initialized)
        initSelect2(materialSelect, row);

        return row;
    }

    function isMaterialDuplicate(value, currentSelect) {
        if (!value) return false;
        let dup = false;
        document.querySelectorAll('.material-select').forEach(sel => {
            if (sel !== currentSelect && String(sel.value) === String(value)) {
                dup = true;
            }
        });
        return dup;
    }

    function bindMaterialEvents(selectEl, row) {
        // Native change updates stock/unit
        selectEl.addEventListener('change', function() {
            updateMaterialStock(this, row);
        });

        // When using Select2, intercept selection to show alert and revert gracefully
        if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
            const $el = jQuery(selectEl);
            // Avoid duplicate bindings
            $el.off('select2:opening.select2dup select2:select.select2dup');
            $el.on('select2:opening.select2dup', function() {
                $el.data('prev', $el.val());
            });
            $el.on('select2:select.select2dup', function(e) {
                const selectedId = e && e.params && e.params.data ? String(e.params.data.id) : String($el.val() || '');
                if (isMaterialDuplicate(selectedId, selectEl)) {
                    // Suppress duplicate handling in change handler to avoid double alerts
                    selectEl.dataset.suppressDuplicate = '1';
                    Swal.fire({
                        title: 'Material Duplikat',
                        text: 'Material ini sudah dipilih di baris lain. Silakan pilih material yang berbeda atau update jumlah yang sudah ada.',
                        icon: 'warning',
                        confirmButtonColor: '#f59e0b'
                    }).then(() => {
                        const prev = $el.data('prev') || null;
                        $el.val(prev).trigger('change');
                        // Re-open so user can pick another option
                        setTimeout(() => { try { $el.select2('open'); } catch (err) {} }, 0);
                    });
                } else {
                    // Non-duplicate selection: update stock/unit immediately
                    updateMaterialStock(selectEl, row);
                }
            });
            // Handle clearing (if allowClear is enabled globally)
            $el.off('select2:clear.select2dup').on('select2:clear.select2dup', function() {
                resetStockDisplay(row.querySelector('.stock-display'), row.querySelector('.unit-display'));
            });
        }
    }

    function initSelect2(selectEl, row) {
        try {
            if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
                const $el = jQuery(selectEl);
                if (!$el.hasClass('select2-hidden-accessible') && !$el.data('select2')) {
                    const dpEl = (row && row.closest && row.closest('.modal .modal-content')) || document.querySelector('.modal.show .modal-content') || document.body;
                    $el.select2({
                        theme: 'bootstrap-5',
                        width: 'style',
                        dropdownParent: jQuery(dpEl)
                    });
                } else {
                    $el.trigger('change.select2');
                }
            } else {
                // Fallback to global initializer if present
                document.dispatchEvent(new Event('reinit-select2'));
                // Retry shortly in case Select2 loads after this script
                setTimeout(() => {
                    try { initSelect2(selectEl, row); } catch (e) {}
                }, 120);
            }
        } catch (e) { /* no-op */ }
    }

    function updateMaterialStock(selectElement, row) {
        const value = (selectElement && typeof selectElement.value !== 'undefined') ? selectElement.value : '';
        const stockDisplay = row.querySelector('.stock-display');
        const unitDisplay = row.querySelector('.unit-display');

        if (!value) {
            // Reset to default state when no material selected
            resetStockDisplay(stockDisplay, unitDisplay);
            return;
        }

        // Check for duplicate material selection using current value
        const isDuplicate = isMaterialDuplicate(value, selectElement);

        if (isDuplicate) {
            // If this was already handled by the select2 handler, skip duplicate alert here
            if (selectElement.dataset && selectElement.dataset.suppressDuplicate === '1') {
                try { delete selectElement.dataset.suppressDuplicate; } catch (e) { selectElement.dataset.suppressDuplicate = ''; }
                return;
            } else {
                // Fallback: handle duplicates even without select2 bindings
                Swal.fire({
                    title: 'Material Duplikat',
                    text: 'Material ini sudah dipilih di baris lain. Silakan pilih material yang berbeda atau update jumlah yang sudah ada.',
                    icon: 'warning',
                    confirmButtonColor: '#f59e0b'
                });
                if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
                    jQuery(selectElement).val(null).trigger('change');
                } else {
                    selectElement.value = '';
                    try { selectElement.dispatchEvent(new Event('change', { bubbles: true })); } catch (e) {}
                }
                resetStockDisplay(stockDisplay, unitDisplay);
                return;
            }
        }

        // Find the selected option safely
        let selectedOption = null;
        for (let i = 0; i < selectElement.options.length; i++) {
            const opt = selectElement.options[i];
            if (opt.value === value) { selectedOption = opt; break; }
        }

        const stock = parseFloat(selectedOption && selectedOption.dataset ? selectedOption.dataset.stock : 0) || 0;
        const unit = (selectedOption && selectedOption.dataset && selectedOption.dataset.unit) ? selectedOption.dataset.unit : '-';

        // Update stock display with proper formatting and color
        updateStockDisplay(stockDisplay, stock);
        unitDisplay.textContent = unit;

        // Auto-focus quantity input for better UX
        const qtyInput = row.querySelector('input[type="number"]');
        if (qtyInput) {
            setTimeout(() => qtyInput.focus(), 100);
        }
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
            stockDisplay.textContent = 'Stok Habis';
        }
    }

    function resetStockDisplay(stockDisplay, unitDisplay) {
        stockDisplay.textContent = '-';
        stockDisplay.className = 'stock-display inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800';
        unitDisplay.textContent = '-';
    }

    window.removeMaterialRow = function(button) {
        const row = button.closest('tr');
        const tbody = document.getElementById('materialsTableBody');
        
        // Keep at least one row
        if (tbody.children.length > 1) {
            row.remove();
        } else {
            // Clear the last row instead of removing it
            const clearSelect = row.querySelector('.material-select');
            if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
                jQuery(clearSelect).val(null).trigger('change');
            } else {
                clearSelect.value = '';
                try { clearSelect.dispatchEvent(new Event('change', { bubbles: true })); } catch (e) {}
            }
            row.querySelector('input[type="number"]').value = '';
            const stockDisplay = row.querySelector('.stock-display');
            const unitDisplay = row.querySelector('.unit-display');
            resetStockDisplay(stockDisplay, unitDisplay);
        }
    };

    // Form validation
    document.getElementById('purchaseOrderForm').addEventListener('submit', function(e) {
        const materialSelects = document.querySelectorAll('.material-select');
        let hasValidMaterial = false;

        materialSelects.forEach(select => {
            if (select.value) {
                hasValidMaterial = true;
            }
        });

        if (!hasValidMaterial) {
            e.preventDefault();
            
            // Show SweetAlert2 error
            Swal.fire({
                title: 'Validation Error',
                text: 'Please select at least one material.',
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
            return false;
        }
    });

    // Initialize supplier select explicitly
    const supplierSelect = document.getElementById('supplier_id');
    if (supplierSelect) initSelect2(supplierSelect);

    // Initialize any existing material selects (if pre-rendered)
    document.querySelectorAll('.material-select').forEach(sel => initSelect2(sel));

    // Also listen for global reinit to ensure late-loaded Select2 initializes all
    document.addEventListener('reinit-select2', function() {
        document.querySelectorAll('select.select2').forEach(sel => initSelect2(sel));
    });

    // On full window load, force re-init in case CSS/Select2 loaded late causing collapsed width
    window.addEventListener('load', function() {
        try {
            if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
                document.querySelectorAll('.material-select').forEach(function(sel) {
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

    // Expose for inline onblur handler
    window.validateQuantity = function(input) {
        const qty = parseFloat(input.value);
        if (qty && qty > 1000) {
            Swal.fire({
                icon: 'warning',
                title: 'Jumlah Besar',
                text: 'Anda memesan dalam jumlah yang besar. Pastikan jumlah sudah benar.',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Periksa Lagi'
            }).then((result) => {
                if (!result.isConfirmed) {
                    input.focus();
                    input.select();
                }
            });
        }
    }

    // Final nudge to (re)initialize all Select2s in case of late script loading
    try { document.dispatchEvent(new Event('reinit-select2')); } catch (e) {}
});
</script>
@endpush

@push('styles')
<link href="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
@endpush