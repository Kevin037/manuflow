@extends('layouts.admin')
@section('title','Payment Details')
@section('content')
<div class="space-y-8">
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
    <div class="flex items-center justify-between">
      <div class="flex items-center">
        <a href="{{ route('payments.index') }}" class="text-gray-400 hover:text-gray-600 mr-6 transition-colors duration-200">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Payment Details</h1>
          <p class="mt-2 text-base text-gray-600">{{ $payment->no }}</p>
        </div>
      </div>
      <div class="flex items-center gap-x-3">
        <a href="{{ route('payments.edit',$payment) }}" class="inline-flex items-center gap-x-2 rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-500">
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
          Edit
        </a>
      </div>
    </div>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-8">
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-semibold text-gray-900">Payment Information</h2>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
              <div><dt class="text-sm font-medium text-gray-500">Payment Number</dt><dd class="mt-1 text-lg font-semibold text-gray-900">{{ $payment->no }}</dd></div>
              <div><dt class="text-sm font-medium text-gray-500">Date</dt><dd class="mt-1 text-sm text-gray-900">{{ optional($payment->paid_at ?? $payment->created_at)->format('d M Y H:i') }}</dd></div>
              <div><dt class="text-sm font-medium text-gray-500">Invoice</dt><dd class="mt-1 text-sm text-gray-900">{{ $payment->invoice?->no }}</dd></div>
            </div>
            <div class="space-y-4">
              <div><dt class="text-sm font-medium text-gray-500">Payment Type</dt><dd class="mt-1">{!! $payment->payment_type_badge !!}</dd></div>
              <div><dt class="text-sm font-medium text-gray-500">Amount</dt><dd class="mt-1 text-2xl font-bold text-primary-600">Rp {{ number_format($payment->amount,0,',','.') }}</dd></div>
              <div><dt class="text-sm font-medium text-gray-500">Created</dt><dd class="mt-1 text-sm text-gray-900">{{ $payment->created_at->format('d M Y H:i') }}</dd></div>
            </div>
          </div>
          @if($payment->payment_type==='transfer')
          <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div><dt class="text-sm font-medium text-gray-500">Account Number</dt><dd class="mt-1 text-sm text-gray-900">{{ $payment->bank_account_id }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Bank Name</dt><dd class="mt-1 text-sm text-gray-900">{{ $payment->bank_account_type }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Account Name</dt><dd class="mt-1 text-sm text-gray-900">{{ $payment->bank_account_name }}</dd></div>
          </div>
          @endif
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200"><h2 class="text-lg font-semibold text-gray-900">Invoice Products</h2></div>
        <div class="overflow-x-auto">
          <table class="w-full divide-y divide-gray-200">
            <thead class="bg-gray-50/50"><tr><th class="px-6 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">#</th><th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Product</th><th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Qty</th><th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Price</th><th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Subtotal</th></tr></thead>
            <tbody class="divide-y divide-gray-200">
              @forelse($payment->invoice?->order?->orderDetails ?? [] as $i=>$detail)
              <tr class="hover:bg-gray-50 transition-colors duration-200">
                <td class="px-6 py-4 text-sm text-gray-900">{{ $i+1 }}</td>
                <td class="px-3 py-4"><div class="text-sm font-medium text-gray-900">{{ $detail->product?->name }}</div></td>
                <td class="px-3 py-4 text-right text-sm text-gray-900">{{ number_format($detail->qty,2) }}</td>
                <td class="px-3 py-4 text-right text-sm text-gray-900">Rp {{ number_format($detail->product?->price ?? 0,0,',','.') }}</td>
                <td class="px-3 py-4 text-right text-sm font-medium text-gray-900">Rp {{ number_format(($detail->product?->price ?? 0)*$detail->qty,0,',','.') }}</td>
              </tr>
              @empty
              <tr><td colspan="5" class="px-6 py-12 text-center"><div class="flex flex-col items-center"><svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg><h3 class="mt-2 text-sm font-medium text-gray-900">No products</h3><p class="mt-1 text-sm text-gray-500">No products found for this invoice.</p></div></td></tr>
              @endforelse
            </tbody>
            @if(($payment->invoice?->order?->orderDetails?->count() ?? 0) > 0)
            <tfoot class="bg-gray-50"><tr><td colspan="4" class="px-6 py-4 text-right text-sm font-semibold text-gray-900">Total:</td><td class="px-3 py-4 text-right text-lg font-bold text-primary-600">Rp {{ number_format($payment->invoice?->order?->total ?? 0,0,',','.') }}</td></tr></tfoot>
            @endif
          </table>
        </div>
      </div>
    </div>
    <div class="space-y-8">
      @if($payment->invoice?->order?->customer)
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-blue-50 px-6 py-4 border-b border-blue-200"><h3 class="text-lg font-semibold text-blue-900">Customer Information</h3></div>
        <div class="p-6 space-y-4">
          <div><dt class="text-sm font-medium text-gray-500">Name</dt><dd class="mt-1 text-sm text-gray-900">{{ $payment->invoice->order->customer?->name }}</dd></div>
          @if($payment->invoice->order->customer?->phone)<div><dt class="text-sm font-medium text-gray-500">Phone</dt><dd class="mt-1 text-sm text-gray-900">{{ $payment->invoice->order->customer->phone }}</dd></div>@endif
          @if($payment->invoice->order->customer?->address)<div><dt class="text-sm font-medium text-gray-500">Address</dt><dd class="mt-1 text-sm text-gray-900">{{ $payment->invoice->order->customer->address }}</dd></div>@endif
        </div>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection