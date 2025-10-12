@php /** @var \App\Models\Payment $payment */ @endphp
<div class="flex items-center gap-x-2">
  @php
    $waLink = null;
    try {
      $p = $payment->loadMissing('invoice.order.customer');
      $invoice = $p->invoice;
      $customer = $invoice?->order?->customer;
      if ($customer && !empty($customer->phone)) {
        $lines = [];
        $lines[] = 'Payment Receipt ' . ($p->no ?? '#');
        $lines[] = 'Date: ' . optional($p->paid_at)->format('Y-m-d');
        if ($invoice?->no) { $lines[] = 'Invoice: ' . $invoice->no; }
        $lines[] = 'Customer: ' . $customer->name;
        $lines[] = '';
        $lines[] = 'Amount: Rp ' . number_format((float)$p->amount, 0, ',', '.');
        $paid = (float)($invoice?->payments?->sum('amount') ?? 0);
        $orderTotal = (float)($invoice?->order?->total ?? 0);
        $remaining = max($orderTotal - $paid, 0);
        $lines[] = 'Paid to date: Rp ' . number_format($paid, 0, ',', '.');
        $lines[] = 'Remaining: Rp ' . number_format($remaining, 0, ',', '.');
        $lines[] = '';
        $lines[] = 'Terima kasih.';

        $message = implode("\n", $lines);
        $phone = preg_replace('/\D+/', '', $customer->phone);
        if (!empty($phone)) {
          if (substr($phone, 0, 1) === '0') { $phone = '62' . substr($phone, 1); }
          $waLink = 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
        }
      }
    } catch (\Throwable $th) { $waLink = null; }
  @endphp
  @if($waLink)
    <a href="{{ $waLink }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-md bg-green-100 p-2 text-green-700 hover:bg-green-200" title="WhatsApp">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
        <path d="M20.52 3.48A11.74 11.74 0 0 0 12 0C5.37 0 0 5.37 0 12c0 2.11.55 4.16 1.59 5.98L0 24l6.18-1.62A11.93 11.93 0 0 0 12 24c6.63 0 12-5.37 12-12 0-3.2-1.25-6.21-3.48-8.52zM12 22a9.93 9.93 0 0 1-5.08-1.41l-.36-.21-3.66.96.98-3.56-.24-.37A9.92 9.92 0 1 1 22 12c0 5.52-4.48 10-10 10zm5.49-7.26c-.3-.15-1.78-.88-2.06-.98-.27-.1-.47-.15-.67.15-.2.3-.77.98-.95 1.18-.17.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.48-1.75-1.65-2.05-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.38-.03-.53-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.5h-.57c-.2 0-.53.08-.8.38-.27.3-1.05 1.03-1.05 2.5s1.08 2.9 1.23 3.1c.15.2 2.13 3.25 5.16 4.56.72.31 1.28.49 1.72.63.72.23 1.38.2 1.9.12.58-.09 1.78-.73 2.03-1.44.25-.71.25-1.32.17-1.45-.08-.13-.27-.2-.57-.35z"/>
      </svg>
    </a>
  @endif
  <a href="{{ route('payments.show',$payment) }}" class="inline-flex items-center justify-center rounded-md bg-gray-100 p-2 text-gray-600 hover:bg-gray-200" title="View">
    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
  </a>
  <a href="{{ route('payments.edit',$payment) }}" class="inline-flex items-center justify-center rounded-md bg-amber-100 p-2 text-amber-700 hover:bg-amber-200" title="Edit">
    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
  </a>
  <a href="{{ route('payments.export',$payment) }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-md bg-indigo-100 p-2 text-indigo-700 hover:bg-indigo-200" title="Export PDF">
    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m4-4H8m8-10H8a2 2 0 00-2 2v12a2 2 0 002 2h4l4 4V4a2 2 0 00-2-2z"/></svg>
  </a>
  <button type="button" data-url="{{ route('payments.destroy',$payment) }}" class="delete-btn inline-flex items-center justify-center rounded-md bg-red-100 p-2 text-red-600 hover:bg-red-200" title="Delete">
    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 4h.01"/></svg>
  </button>
</div>