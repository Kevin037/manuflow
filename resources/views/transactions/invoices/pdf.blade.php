<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $invoice->no }} - Invoice</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color:#111; }
        h1 { font-size: 20px; margin:0 0 8px; }
        .meta { margin-bottom: 18px; }
        .meta table { width:100%; border-collapse:collapse; }
        .meta td { padding:4px 6px; vertical-align:top; }
        .meta td.label { font-weight:600; width:120px; }
        table.items { width:100%; border-collapse:collapse; margin-top:10px; }
        table.items th, table.items td { border:1px solid #ddd; padding:6px 8px; }
        table.items th { background:#f3f4f6; text-align:left; font-size:11px; letter-spacing:.5px; }
        tfoot td { font-weight:600; }
        .text-right { text-align:right; }
        .badge { display:inline-block; padding:2px 8px; border-radius:12px; font-size:10px; font-weight:600; }
        .badge-paid { background:#dcfce7; color:#166534; }
        .badge-pending { background:#fef9c3; color:#854d0e; }
        .footer { margin-top:32px; font-size:10px; color:#555; text-align:center; }
    </style>
</head>
<body>
    <h1>Invoice</h1>
    <div class="meta">
        <table>
            <tr>
                <td class="label">Invoice No</td><td>: {{ $invoice->no }}</td>
                <td class="label">Date</td><td>: {{ $invoice->dt->format('d M Y') }}</td>
            </tr>
            <tr>
                <td class="label">Order No</td><td>: {{ $invoice->order?->no }}</td>
                <td class="label">Customer</td><td>: {{ $invoice->order?->customer?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td colspan="3">: @if($invoice->computed_status==='paid')<span class="badge badge-paid">PAID</span>@else<span class="badge badge-pending">PENDING</span>@endif</td>
            </tr>
        </table>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th style="width:40px">#</th>
                <th>Product</th>
                <th style="width:80px" class="text-right">Qty</th>
                <th style="width:90px" class="text-right">Price</th>
                <th style="width:110px" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $i=1; $total = $invoice->order?->total ?? 0; @endphp
            @forelse($invoice->order?->orderDetails ?? [] as $detail)
                @php $price = $detail->product?->price ?? 0; $subtotal = $price * $detail->qty; @endphp
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $detail->product?->name }}</td>
                    <td class="text-right">{{ number_format($detail->qty,2,',','.') }}</td>
                    <td class="text-right">{{ number_format($price,0,',','.') }}</td>
                    <td class="text-right">{{ number_format($subtotal,0,',','.') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center; padding:16px">No products.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right">Total</td>
                <td class="text-right">{{ number_format($total,0,',','.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Generated at {{ $generatedAt->format('d M Y H:i') }} | Manuflow ERP
    </div>
</body>
</html>
