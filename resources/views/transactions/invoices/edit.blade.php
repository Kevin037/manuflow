@extends('layouts.admin')
@section('title','Edit Invoice')
@section('content')
<div class="space-y-8">
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
    <div class="flex items-center">
        <a href="{{ route('invoices.index') }}" class="text-gray-400 hover:text-gray-600 mr-6 transition-colors duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Invoice</h1>
            <p class="mt-2 text-base text-gray-600">Update invoice information</p>
        </div>
    </div>
  </div>
  <form method="POST" action="{{ route('invoices.update',$invoice) }}" id="invoiceForm">
    @csrf
    @method('PUT')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="bg-gray-50 px-8 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Invoice Information</h2>
        </div>
        <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2" for="dt">Invoice Date *</label>
                    <input type="date" id="dt" name="dt" value="{{ old('dt',$invoice->dt->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2" for="order_id">Order *</label>
                    <select id="order_id" name="order_id" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" required>
                        <option value="">Select Order</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->id }}" data-total="{{ $order->total }}" data-customer="{{ $order->customer?->name }}" data-details='@json($order->orderDetails->map(fn($d)=>["product"=>$d->product?->name,"qty"=>$d->qty]))' {{ $invoice->order_id==$order->id?'selected':'' }}>{{ $order->no }} - {{ $order->customer?->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div id="orderSummary" class="hidden">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Customer</dt>
                        <dd id="summaryCustomer" class="mt-1 text-sm text-gray-900">-</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total</dt>
                        <dd id="summaryTotal" class="mt-1 text-2xl font-bold text-primary-600">Rp 0</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Products</dt>
                        <dd id="summaryProducts" class="mt-1 text-sm text-gray-900">-</dd>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flex items-center justify-end gap-x-4">
        <a href="{{ route('invoices.index') }}" class="inline-flex items-center gap-x-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
        <button type="submit" class="inline-flex items-center gap-x-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-500">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Update Invoice
        </button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
const formatRupiah = (n)=>'Rp '+Number(n||0).toLocaleString('id-ID');

document.addEventListener('DOMContentLoaded',()=>{
  const orderSelect = document.getElementById('order_id');
  const summary = document.getElementById('orderSummary');
  const customerEl = document.getElementById('summaryCustomer');
  const totalEl = document.getElementById('summaryTotal');
  const productsEl = document.getElementById('summaryProducts');

  function updateSummary(){
    const opt = orderSelect.options[orderSelect.selectedIndex];
    if(!opt.value){ summary.classList.add('hidden'); return; }
    const total = opt.dataset.total || 0;
    const customer = opt.dataset.customer || '-';
    let details = [];
    try { details = JSON.parse(opt.dataset.details||'[]'); } catch(e){}
    customerEl.textContent = customer;
    totalEl.textContent = formatRupiah(total);
    productsEl.textContent = details.map(d=>`${d.product} (${d.qty})`).join(', ');
    summary.classList.remove('hidden');
  }

  orderSelect.addEventListener('change', updateSummary);
  updateSummary();
});
</script>
@endpush