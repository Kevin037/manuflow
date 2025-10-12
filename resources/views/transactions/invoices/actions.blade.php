<div class="flex justify-end gap-x-2">
    @php
        $waLink = null;
        try {
            $inv = $invoice->loadMissing(['order.customer','order.orderDetails.product']);
            $customer = $inv->order?->customer;
            if ($customer && !empty($customer->phone)) {
                $lines = [];
                $lines[] = 'Invoice ' . ($inv->no ?? '#');
                $lines[] = 'Date: ' . $inv->dt->format('Y-m-d');
                if ($inv->order?->no) { $lines[] = 'Order: ' . $inv->order->no; }
                $lines[] = 'Customer: ' . $customer->name;
                $lines[] = '';
                $lines[] = 'Items:';
                foreach (($inv->order?->orderDetails ?? []) as $d) {
                    $prodName = optional($d->product)->name ?? 'Unknown';
                    $unit = optional($d->product)->unit ?? '';
                    $qty = rtrim(rtrim(number_format((float)$d->qty, 2, '.', ''), '0'), '.');
                    $lines[] = '• ' . $prodName . ' — ' . $qty . ($unit ? (' ' . $unit) : '');
                }
                $lines[] = '';
                $lines[] = 'Total: Rp ' . number_format((float)($inv->order?->total ?? 0), 0, ',', '.');
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
        <a href="{{ $waLink }}" target="_blank" rel="noopener" 
           class="inline-flex items-center gap-x-1.5 rounded-lg bg-green-50 px-2.5 py-1.5 text-xs font-medium text-green-700 hover:bg-green-100 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3 w-3">
              <path d="M20.52 3.48A11.74 11.74 0 0 0 12 0C5.37 0 0 5.37 0 12c0 2.11.55 4.16 1.59 5.98L0 24l6.18-1.62A11.93 11.93 0 0 0 12 24c6.63 0 12-5.37 12-12 0-3.2-1.25-6.21-3.48-8.52zM12 22a9.93 9.93 0 0 1-5.08-1.41l-.36-.21-3.66.96.98-3.56-.24-.37A9.92 9.92 0 1 1 22 12c0 5.52-4.48 10-10 10zm5.49-7.26c-.3-.15-1.78-.88-2.06-.98-.27-.1-.47-.15-.67.15-.2.3-.77.98-.95 1.18-.17.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.48-1.75-1.65-2.05-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.38-.03-.53-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.5h-.57c-.2 0-.53.08-.8.38-.27.3-1.05 1.03-1.05 2.5s1.08 2.9 1.23 3.1c.15.2 2.13 3.25 5.16 4.56.72.31 1.28.49 1.72.63.72.23 1.38.2 1.9.12.58-.09 1.78-.73 2.03-1.44.25-.71.25-1.32.17-1.45-.08-.13-.27-.2-.57-.35z"/>
            </svg>
        </a>
    @endif
    <a href="{{ route('invoices.show',$invoice) }}" class="inline-flex items-center gap-x-1.5 rounded-lg bg-gray-50 px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-100 transition-colors duration-200">
        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        View
    </a>
    <a href="{{ route('invoices.edit',$invoice) }}" class="inline-flex items-center gap-x-1.5 rounded-lg bg-primary-50 px-2.5 py-1.5 text-xs font-medium text-primary-600 hover:bg-primary-100 transition-colors duration-200">
        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Edit
    </a>
    <a href="{{ route('invoices.export',$invoice) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-x-1.5 rounded-lg bg-indigo-50 px-2.5 py-1.5 text-xs font-medium text-indigo-600 hover:bg-indigo-100 transition-colors duration-200">
        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        PDF
    </a>
    <button class="delete-btn inline-flex items-center gap-x-1.5 rounded-lg bg-red-50 px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100 transition-colors duration-200" data-url="{{ route('invoices.destroy',$invoice) }}">
        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        Delete
    </button>
</div>