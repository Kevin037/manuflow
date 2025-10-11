@extends('layouts.admin')
@section('title','Create Payment')
@section('content')
<div class="space-y-8">
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
    <div class="flex items-center">
      <a href="{{ route('payments.index') }}" class="text-gray-400 hover:text-gray-600 mr-6 transition-colors duration-200">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      </a>
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Create Payment</h1>
        <p class="mt-2 text-base text-gray-600">Record a payment for an invoice</p>
      </div>
    </div>
  </div>
  <form method="POST" action="{{ route('payments.store') }}" id="paymentForm">
    @csrf
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
      <div class="bg-gray-50 px-8 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Payment Information</h2>
      </div>
      <div class="p-8 space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2" for="dt">Payment Date *</label>
            <input type="date" id="dt" name="dt" value="{{ old('dt',date('Y-m-d')) }}" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" required>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2" for="invoice_id">Invoice *</label>
            <select id="invoice_id" name="invoice_id" class="select2 w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" required>
              <option value="">Select Invoice</option>
              @foreach($invoices as $invoice)
              @php $order = $invoice->order; $details = $order?->orderDetails->map(fn($d)=>['product'=>$d->product?->name,'qty'=>$d->qty,'price'=>$d->product?->price,'subtotal'=>$d->qty * ($d->product?->price ?? 0)]); @endphp
              <option value="{{ $invoice->id }}" data-total="{{ $order?->total ?? 0 }}" data-customer="{{ $order?->customer?->name }}" data-details='@json($details)' data-paid="{{ $invoice->payments->sum('amount') }}">{{ $invoice->no }} - {{ $order?->customer?->name }} ({{ 'Rp '.number_format($order?->total ?? 0,0,',','.') }})</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2" for="payment_type">Payment Type *</label>
            <select id="payment_type" name="payment_type" class="select2 w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" required>
              <option value="transfer" {{ old('payment_type')==='transfer'?'selected':'' }}>Transfer</option>
              <option value="cash" {{ old('payment_type')==='cash'?'selected':'' }}>Cash</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2" for="amount">Amount *</label>
            <input type="number" step="0.01" id="amount" name="amount" value="{{ old('amount') }}" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="0" required>
            <p class="mt-2 text-xs text-gray-500" id="amountHelp"></p>
          </div>
        </div>
        <div id="bankFields" class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2" for="bank_account_id">Account Number *</label>
            <input type="text" id="bank_account_id" name="bank_account_id" value="{{ old('bank_account_id') }}" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2" for="bank_account_type">Bank Name *</label>
            <input type="text" id="bank_account_type" name="bank_account_type" value="{{ old('bank_account_type') }}" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
          </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2" for="bank_account_name">Account Name *</label>
              <input type="text" id="bank_account_name" name="bank_account_name" value="{{ old('bank_account_name') }}" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
        </div>
        <div id="invoiceSummary" class="hidden">
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
              <dt class="text-sm font-medium text-gray-500">Paid</dt>
              <dd id="summaryPaid" class="mt-1 text-sm text-gray-900">Rp 0</dd>
            </div>
          </div>
          <div class="mt-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Products</h3>
            <ul id="summaryProducts" class="text-sm text-gray-900 space-y-1 list-disc list-inside"></ul>
          </div>
        </div>
      </div>
    </div>
    <div class="flex items-center justify-end gap-x-4">
      <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-x-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
      <button type="submit" class="inline-flex items-center gap-x-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-500">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        Save Payment
      </button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
const formatRupiah=n=>'Rp '+Number(n||0).toLocaleString('id-ID');

document.addEventListener('DOMContentLoaded',()=>{
  const invoiceSelect=document.getElementById('invoice_id');
  const summary=document.getElementById('invoiceSummary');
  const paidEl=document.getElementById('summaryPaid');
  const customerEl=document.getElementById('summaryCustomer');
  const totalEl=document.getElementById('summaryTotal');
  const productsEl=document.getElementById('summaryProducts');
  const amountInput=document.getElementById('amount');
  const amountHelp=document.getElementById('amountHelp');
  const paymentType=document.getElementById('payment_type');
  const bankFields=document.getElementById('bankFields');

  function toggleBank(){
    if(paymentType.value==='transfer'){ bankFields.classList.remove('hidden'); bankFields.querySelectorAll('input').forEach(i=>i.required=true); }
    else { bankFields.classList.add('hidden'); bankFields.querySelectorAll('input').forEach(i=>{i.required=false; i.value='';}); }
  }

  function updateSummary(){
    const opt=invoiceSelect.options[invoiceSelect.selectedIndex];
    if(!opt.value){ summary.classList.add('hidden'); return; }
    summary.classList.remove('hidden');
    const customer=opt.dataset.customer||'-';
    const total=opt.dataset.total||0;
    const paid=opt.dataset.paid||0;
    let details=[]; try{ details=JSON.parse(opt.dataset.details||'[]'); }catch(e){}
    customerEl.textContent=customer; totalEl.textContent=formatRupiah(total); paidEl.textContent=formatRupiah(paid);
    productsEl.innerHTML='';
    details.forEach(d=>{ const li=document.createElement('li'); li.textContent=`${d.product} - ${d.qty} x Rp ${Number(d.price||0).toLocaleString('id-ID')} = Rp ${Number(d.subtotal||0).toLocaleString('id-ID')}`; productsEl.appendChild(li); });
    const remaining = (total - paid); amountHelp.textContent='Remaining: '+formatRupiah(remaining);
    if(!amountInput.value && remaining>0){ amountInput.value = remaining; }
  }

  invoiceSelect.addEventListener('change', updateSummary);
  paymentType.addEventListener('change', toggleBank);
  toggleBank();
  updateSummary();
});
</script>
@endpush