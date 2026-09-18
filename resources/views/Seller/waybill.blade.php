<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Waybill {{ $order->order_number }}</title>
<style>
body{font-family:Arial,sans-serif;background:#fff;color:#231815;margin:0;padding:28px}.waybill{max-width:780px;margin:auto;border:2px solid #231815}.head,.section{padding:18px 22px;border-bottom:1px solid #231815}.head{display:flex;justify-content:space-between;align-items:flex-start}.brand{font-size:30px;font-weight:800;color:#641f19}.grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}.label{font-size:11px;text-transform:uppercase;color:#777}.value{font-size:15px;font-weight:700;margin-top:4px}.items{width:100%;border-collapse:collapse}.items th,.items td{padding:10px;border-bottom:1px solid #ddd;text-align:left}.codes{display:grid;grid-template-columns:190px 1fr;gap:24px;align-items:center}.qr svg{display:block;width:170px;height:170px}.barcode svg{display:block;width:100%;height:76px}.tracking{text-align:center;font:700 16px monospace;letter-spacing:2px;margin-top:10px}.actions{text-align:center;margin-top:18px}@media(max-width:600px){.grid,.codes{grid-template-columns:1fr}.head{gap:16px;flex-direction:column}}@media print{.actions{display:none}body{padding:0}.waybill{border:1px solid #000}}
</style>
</head>
<body>
<div class="waybill">
    <div class="head"><div><div class="brand">LIKHAE</div><div>Marketplace Parcel Waybill</div></div><div><div class="label">Order</div><div class="value">#{{ $order->order_number }}</div><div class="label" style="margin-top:8px">Tracking</div><div class="value">{{ $delivery->tracking_number }}</div></div></div>
    <div class="section grid"><div><div class="label">From</div><div class="value">{{ $seller->store_name ?: $seller->business_name ?: $seller->name }}</div><div>{{ collect([$seller->house_number,$seller->street,$seller->barangay,$seller->municipality,$seller->province])->filter()->implode(', ') }}</div></div><div><div class="label">To</div><div class="value">{{ $order->buyer?->name }}</div><div>{{ $order->shipping_address }}</div></div></div>
    <div class="section"><table class="items"><thead><tr><th>Item</th><th>Qty</th><th>Unit Price</th><th>Subtotal</th></tr></thead><tbody>@foreach($order->items as $item)<tr><td>{{ $item->product?->name }}</td><td>{{ $item->quantity }}</td><td>₱{{ number_format($item->unit_price,2) }}</td><td>₱{{ number_format($item->subtotal,2) }}</td></tr>@endforeach</tbody></table></div>
    <div class="section grid"><div><div class="label">Payment</div><div class="value">{{ strtoupper((string)$order->payment_method) }}</div></div><div><div class="label">Total</div><div class="value">₱{{ number_format($order->total_amount,2) }}</div></div></div>
    <div class="section codes"><div class="qr">{!! $qrSvg !!}</div><div><div class="barcode">{!! $barcodeSvg !!}</div><div class="tracking">{{ $delivery->tracking_number }}</div><div class="label" style="text-align:center;margin-top:8px">Scan QR or Code 128 to resolve this parcel</div></div></div>
</div>
<div class="actions"><button onclick="window.print()">Print Waybill</button></div>
</body>
</html>
