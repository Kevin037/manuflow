<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment {{ $payment->no }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size:12px; color:#111; }
    .header { display:flex; justify-content:space-between; margin-bottom:20px; }
    .title { font-size:20px; font-weight:700; }
    table { width:100%; border-collapse:collapse; margin-top:20px; }
    th,td { padding:8px 10px; border:1px solid #ccc; }
    th { background:#f3f4f6; text-align:left; font-weight:600; }
    .text-right { text-align:right; }
    .badge { display:inline-block; padding:4px 10px; border-radius:999px; font-size:10px; font-weight:600; }
    .badge-cash { background:#d1fae5; color:#065f46; }
    .badge-transfer { background:#dbeafe; color:#1e3a8a; }
  </style>
</head>
<body>
  <div class="header">
    <div>
      <div class="title">PAYMENT</div>
      <div style="margin-top:4px;">No: <strong>{{ $payment->no }}</strong></div>
      <div>Date: {{ optional($payment->paid_at ?? $payment->created_at)->format('d M Y H:i') }}</div>
    </div>
    <div style="text-align:right;">
      <div><strong>Company Name</strong></div>
      <div>Address line 1</div>
      <div>Address line 2</div>
    </div>
  </div>
  <div style="margin-top:10px;">
    <strong>Invoice:</strong> {{ $payment->invoice?->no }}<br>
    <strong>Customer:</strong> {{ $payment->invoice?->order?->customer?->name }}<br>
    <strong>Payment Type:</strong> <span class="badge badge-{{ $payment->payment_type }}">{{ ucfirst($payment->payment_type) }}</span><br>
    <strong>Amount:</strong> Rp {{ number_format($payment->amount,0,',','.') }}
  </div>
  @if($payment->payment_type==='transfer')
  <div style="margin-top:10px;">
    <strong>Bank Information</strong><br>
    Account Number: {{ $payment->bank_account_id }}<br>
    Bank Name: {{ $payment->bank_account_type }}<br>
    Account Name: {{ $payment->bank_account_name }}
  </div>
  @endif
  <table>
    <thead>
      <tr>
        <th style="width:40px;">#</th>
        <th>Product</th>
        <th class="text-right">Qty</th>
        <th class="text-right">Price</th>
        <th class="text-right">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      @php $i=1; $total=0; @endphp
      @foreach($payment->invoice?->order?->orderDetails ?? [] as $d)
        @php $subtotal = ($d->product?->price ?? 0) * $d->qty; $total += $subtotal; @endphp
        <tr>
          <td>{{ $i++ }}</td>
            <td>{{ $d->product?->name }}</td>
            <td class="text-right">{{ number_format($d->qty,2) }}</td>
            <td class="text-right">Rp {{ number_format($d->product?->price ?? 0,0,',','.') }}</td>
            <td class="text-right">Rp {{ number_format($subtotal,0,',','.') }}</td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="4" class="text-right"><strong>Total</strong></td>
        <td class="text-right"><strong>Rp {{ number_format($total,0,',','.') }}</strong></td>
      </tr>
    </tfoot>
  </table>
  <div style="margin-top:30px; font-size:11px; color:#555;">Generated at {{ $generatedAt->format('d M Y H:i') }}</div>
</body>
</html>