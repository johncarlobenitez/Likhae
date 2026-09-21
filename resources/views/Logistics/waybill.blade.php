<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waybill {{ $parcel['tracking'] ?? $tracking ?? '' }}</title>
    <style>
        body { margin: 0; background: #f7f0e8; color: #2f1d18; font-family: Arial, sans-serif; }
        .sheet { width: min(820px, calc(100% - 32px)); margin: 24px auto; background: #fffdf9; border: 1px solid #eadccc; padding: 28px; }
        .head { display: flex; justify-content: space-between; gap: 24px; border-bottom: 2px solid #561c17; padding-bottom: 16px; }
        h1 { margin: 0; font-size: 28px; color: #561c17; }
        h2 { margin: 24px 0 10px; font-size: 15px; color: #561c17; text-transform: uppercase; letter-spacing: .08em; }
        .tracking { text-align: right; font-weight: 700; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .box { border: 1px solid #eadccc; padding: 14px; min-height: 110px; }
        .label { color: #8b6f60; font-size: 12px; text-transform: uppercase; letter-spacing: .08em; }
        .value { margin-top: 6px; font-weight: 700; white-space: pre-line; }
        .barcode { margin-top: 22px; border: 1px dashed #561c17; padding: 18px; text-align: center; font-size: 22px; letter-spacing: .15em; }
        .actions { width: min(820px, calc(100% - 32px)); margin: 0 auto 24px; display: flex; gap: 10px; justify-content: flex-end; }
        .btn { border: 1px solid #561c17; background: #561c17; color: #fff; padding: 10px 14px; text-decoration: none; border-radius: 8px; font-size: 13px; cursor: pointer; }
        @media print { body { background: #fff; } .actions { display: none; } .sheet { margin: 0; width: auto; border: 0; } }
    </style>
</head>
<body>
    <div class="actions">
        <a class="btn" href="{{ route('logistics.parcels.show', $delivery) }}">Back</a>
        <button class="btn" onclick="window.print()">Print</button>
    </div>

    <main class="sheet">
        <header class="head">
            <div>
                <h1>LIKHAE Logistics</h1>
                <div class="label">Official parcel waybill</div>
            </div>
            <div class="tracking">
                <div class="label">Tracking Number</div>
                <div>{{ $parcel['tracking'] ?? $tracking }}</div>
            </div>
        </header>

        <section class="grid">
            <div>
                <h2>Seller</h2>
                <div class="box">
                    <div class="value">{{ $delivery->sellerOrder?->seller?->name ?: 'Seller not found' }}</div>
                    <div class="value">{{ $delivery->sellerOrder?->seller?->pickupAddress?->formatted() ?: 'No seller address recorded' }}</div>
                </div>
            </div>
            <div>
                <h2>Recipient</h2>
                <div class="box">
                    <div class="value">{{ $delivery->sellerOrder?->order?->buyer?->name ?: 'Buyer not found' }}</div>
                    <div class="value">{{ collect($delivery->sellerOrder?->order?->shipping_address_snapshot ?? [])->only(['line1','barangay','city','province','postal_code'])->filter()->implode(', ') ?: 'No delivery address recorded' }}</div>
                </div>
            </div>
        </section>

        <h2>Parcel</h2>
        <div class="box">
            <div class="label">Order</div>
            <div class="value">{{ $delivery->sellerOrder?->order?->reference ?: 'Seller order #'.$delivery->seller_order_id }}</div>
            <div class="label" style="margin-top:12px;">Items</div>
            <div class="value">
                @forelse($delivery->sellerOrder?->items ?? [] as $item)
                    {{ $item->quantity }} x {{ $item->product_name ?: 'Product #'.$item->product_id }}<br>
                @empty
                    No order items recorded.
                @endforelse
            </div>
        </div>

        <div class="barcode">{{ $parcel['tracking'] ?? $tracking }}</div>
    </main>
</body>
</html>
