@extends('layouts.admin')

@section('title', 'Edit Sales Order')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="flex items-center">
            <a href="{{ route('sales-orders.index') }}" class="text-gray-400 hover:text-gray-600 mr-6 transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Sales Order</h1>
                <p class="mt-2 text-base text-gray-600">Update sales order details and product quantities</p>
            </div>
        </div>
    </div>

    @if($errors->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-start gap-2">
            <svg class="w-5 h-5 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <span>{{ $errors->first('error') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('sales-orders.update', $order->id) }}" id="salesOrderForm">
        @csrf
        @method('PUT')

        <!-- Basic Information Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="bg-gray-50 px-8 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Sales Order Information</h2>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <label for="dt" class="block text-sm font-semibold text-gray-700 mb-2">Sales Order Date *</label>
                        <input type="date" name="dt" id="dt" value="{{ old('dt', $order->dt->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 @error('dt') border-red-500 ring-2 ring-red-200 @enderror">
                        @error('dt')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <div>
                        <label for="customer_id" class="block text-sm font-semibold text-gray-700 mb-2">Customer *</label>
                        <select name="customer_id" id="customer_id" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 @error('customer_id') border-red-500 ring-2 ring-red-200 @enderror">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id', $order->customer_id) == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
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
                <button type="button" id="addProductBtn" class="inline-flex items-center gap-x-2 rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white hover:bg-primary-500 transition-colors duration-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Product
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200" id="productsTable">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Product</th>
                            <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Current Stock</th>
                            <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Unit</th>
                            <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Quantity</th>
                            <th class="px-3 py-4 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody" class="divide-y divide-gray-200"></tbody>
                </table>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-x-4">
            <a href="{{ route('sales-orders.show', $order->id) }}" class="inline-flex items-center gap-x-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600 transition-all duration-200">Cancel</a>
            <button type="submit" class="inline-flex items-center gap-x-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-all duration-200">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Update Sales Order
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const productsData = @json($productsForJs);
        const existingOrderDetails = @json($orderDetailsForJs);
        let rowIndex = 0;
        if (existingOrderDetails.length) { existingOrderDetails.forEach(detail => addProductRow(detail)); } else { addProductRow(); }
        document.getElementById('addProductBtn').addEventListener('click', () => addProductRow());
        function addProductRow(existingDetail = null) {
            const tbody = document.getElementById('productsTableBody');
            const currentIndex = rowIndex;
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 transition-colors duration-200';
            const selectedProductId = existingDetail ? existingDetail.product_id : '';
            const existingQty = existingDetail ? existingDetail.qty : '';
            row.innerHTML = `
                <td class="px-6 py-4">
                    <select class="product-select w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200" name="products[${currentIndex}][product_id]" required>
                        <option value="">Select Product</option>
                        ${productsData.map(p => `<option value="${p.id}" data-stock="${p.qty}" data-unit="${p.unit}" ${selectedProductId == p.id ? 'selected' : ''}>${p.name} (${p.sku})</option>`).join('')}
                    </select>
                    ${existingDetail ? `<input type="hidden" name="products[${currentIndex}][existing_id]" value="${existingDetail.id}">` : ''}
                </td>
                <td class="px-3 py-4"><span class="stock-display inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">-</span></td>
                <td class="px-3 py-4"><span class="unit-display text-sm text-gray-600">-</span></td>
                <td class="px-3 py-4"><input type="number" class="qty-input w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200" name="products[${currentIndex}][qty]" step="0.01" min="0.01" placeholder="0.00" required value="${existingQty}" data-stock="0" data-original-qty="${existingQty}"></td>
                <td class="px-3 py-4 text-center"><button type="button" class="inline-flex items-center rounded-lg bg-red-50 p-1.5 text-red-700 hover:bg-red-100 transition-colors duration-200 remove-row" onclick="removeProductRow(this)"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></td>`;
            tbody.appendChild(row); rowIndex++;
            const productSelect = row.querySelector('.product-select');
            const qtyInput = row.querySelector('.qty-input');
            productSelect.addEventListener('change', function() { updateProductStock(this, row); });
            qtyInput.addEventListener('input', function() { validateQuantityRealTime(this); });
            qtyInput.addEventListener('blur', function() { validateQuantity(this); });
            if (existingDetail && selectedProductId) { updateProductStock(productSelect, row, true); }
        }
        function updateProductStock(selectElement, row, isInit = false) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const stockDisplay = row.querySelector('.stock-display');
            const unitDisplay = row.querySelector('.unit-display');
            const qtyInput = row.querySelector('.qty-input');
            if (selectedOption && selectedOption.value) {
                const allSelects = document.querySelectorAll('.product-select');
                let duplicate = false; allSelects.forEach(sel => { if (sel !== selectElement && sel.value === selectedOption.value) duplicate = true; });
                if (duplicate && !isInit) { Swal.fire({ title: 'Product Duplicate', text: 'This product is already selected in another row.', icon: 'warning', confirmButtonColor: '#f59e0b' }); selectElement.value=''; resetStockDisplay(stockDisplay, unitDisplay, qtyInput); return; }
                const baseStock = parseFloat(selectedOption.dataset.stock) || 0;
                const originalQty = parseFloat(qtyInput.getAttribute('data-original-qty')) || 0;
                const availableStock = baseStock + originalQty;
                const unit = selectedOption.dataset.unit || 'pcs';
                updateStockDisplay(stockDisplay, availableStock);
                unitDisplay.textContent = unit;
                qtyInput.setAttribute('data-stock', availableStock);
                if (!isInit) setTimeout(() => qtyInput.focus(), 100);
            } else { resetStockDisplay(stockDisplay, unitDisplay, qtyInput); }
        }
        function updateStockDisplay(el, stock) { el.textContent = stock.toLocaleString('id-ID'); if (stock > 10) { el.className='stock-display inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800'; } else if (stock > 0) { el.className='stock-display inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800'; } else { el.className='stock-display inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-100 text-red-800'; el.textContent='Out of Stock'; } }
        function resetStockDisplay(stockDisplay, unitDisplay, qtyInput) { stockDisplay.textContent='-'; stockDisplay.className='stock-display inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800'; unitDisplay.textContent='-'; qtyInput.setAttribute('data-stock','0'); if (!qtyInput.getAttribute('data-original-qty')) qtyInput.value=''; }
        function validateQuantityRealTime(input) { const qty=parseFloat(input.value); const stock=parseFloat(input.getAttribute('data-stock'))||0; if (qty && stock>0 && qty>stock) { input.classList.add('border-red-500','ring-2','ring-red-200'); input.classList.remove('border-gray-300'); if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('stock-warning')) { const w=document.createElement('p'); w.className='stock-warning mt-1 text-xs text-red-600 flex items-center'; w.innerHTML=`<svg class=\"w-3 h-3 mr-1\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path fill-rule=\"evenodd\" d=\"M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd\"/> Insufficient stock! Available: ${stock.toLocaleString('id-ID')}`; input.parentNode.appendChild(w); } } else { input.classList.remove('border-red-500','ring-2','ring-red-200'); input.classList.add('border-gray-300'); const w=input.parentNode.querySelector('.stock-warning'); if (w) w.remove(); } }
        function validateQuantity(input) { const qty=parseFloat(input.value); const stock=parseFloat(input.getAttribute('data-stock'))||0; if (qty && stock>0 && qty>stock) { Swal.fire({ title:'Insufficient Stock!', html:`<div class=\"text-left\"><p class=\"text-gray-600 mb-2\">The requested quantity exceeds available stock:</p><div class=\"bg-gray-50 p-3 rounded-lg\"><p><strong>Requested:</strong> ${qty.toLocaleString('id-ID')}</p><p><strong>Available:</strong> ${stock.toLocaleString('id-ID')}</p><p class=\"text-red-600\"><strong>Shortage:</strong> ${(qty-stock).toLocaleString('id-ID')}</p></div></div>`, icon:'error', confirmButtonColor:'#ef4444', confirmButtonText:'I Understand'}).then(()=>{ input.focus(); input.select(); }); } }
        window.removeProductRow = function(button) { const row=button.closest('tr'); const tbody=document.getElementById('productsTableBody'); if (tbody.children.length>1) { row.remove(); } else { row.querySelector('.product-select').value=''; const stockDisplay=row.querySelector('.stock-display'); const unitDisplay=row.querySelector('.unit-display'); const qtyInput=row.querySelector('.qty-input'); resetStockDisplay(stockDisplay,unitDisplay,qtyInput); qtyInput.value=''; } };
        document.getElementById('salesOrderForm').addEventListener('submit', function(e){ const productSelects=document.querySelectorAll('.product-select'); const qtyInputs=document.querySelectorAll('.qty-input'); let hasValid=false; let stockIssue=false; productSelects.forEach((sel,i)=>{ if(sel.value){ hasValid=true; const q=parseFloat(qtyInputs[i].value); const st=parseFloat(qtyInputs[i].getAttribute('data-stock'))||0; if(q && st>0 && q>st) stockIssue=true; }}); if(!hasValid){ e.preventDefault(); Swal.fire({ title:'Validation Error', text:'Please select at least one product.', icon:'error', confirmButtonColor:'#ef4444'}); return false;} if(stockIssue){ e.preventDefault(); Swal.fire({ title:'Stock Validation Error', text:'Some products have insufficient stock. Please adjust quantities.', icon:'error', confirmButtonColor:'#ef4444'}); return false;} });
    });
</script>
@endpush

@push('styles')
<link href="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
@endpush