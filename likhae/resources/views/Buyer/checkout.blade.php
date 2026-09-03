@extends('layouts.buyer')
@section('title', 'Checkout — LIKHAE')
@section('active', 'cart')

@section('content')
@php
    $cartItems = $cartItems ?? collect([
        ['id'=>1,'name'=>'Handwoven Everyday Tote Bag','variant'=>'Natural Brown','price'=>849.00,'quantity'=>1,'seller'=>'Habi Local Crafts','image'=>null],
        ['id'=>2,'name'=>'Minimalist Ceramic Coffee Mug','variant'=>'Cream · 350 ml','price'=>329.00,'quantity'=>2,'seller'=>'Clay & Co. Studio','image'=>null],
    ]);
    $cartItems = collect($cartItems);
    $subtotal  = $cartItems->sum(fn($i) => data_get($i,'price',0) * data_get($i,'quantity',1));
    $shipping  = $subtotal > 0 ? 80 : 0;
    $discount  = 0;
    $total     = $subtotal + $shipping - $discount;

    /* ── Pull from authenticated user ── */
    $user = auth()->user();
    $userName  = data_get($user, 'name', '');
    $nameParts = explode(' ', trim($userName), 2);
    $firstName = $nameParts[0] ?? '';
    $lastName  = $nameParts[1] ?? '';
    $userPhone = data_get($user, 'phone', '');

    /* Saved addresses — replace with real DB query when available */
    $savedAddresses = [
        [
            'id'       => 1,
            'label'    => 'Home',
            'name'     => $userName ?: 'Juan Dela Cruz',
            'phone'    => $userPhone ?: '09123456789',
            'address'  => '123 Sample Street, Barangay Poblacion',
            'city'     => 'Santa Cruz',
            'province' => 'Laguna',
            'zip'      => '4009',
            'default'  => true,
        ],
        [
            'id'       => 2,
            'label'    => 'Office',
            'name'     => $userName ?: 'Juan Dela Cruz',
            'phone'    => $userPhone ?: '09123456789',
            'address'  => '45 Marketplace Avenue, Barangay Bubukal',
            'city'     => 'Santa Cruz',
            'province' => 'Laguna',
            'zip'      => '4009',
            'default'  => false,
        ],
    ];

    $defaultAddress = collect($savedAddresses)->firstWhere('default', true) ?? $savedAddresses[0] ?? null;
@endphp

<div class="lk-page">

    <div class="lk-page-title">
        <div>
            <span class="lk-kicker">Almost There</span>
            <h1>Checkout</h1>
            <p>Review your order and complete your purchase.</p>
        </div>
        <a href="{{ route('buyer.cart') }}" class="lk-btn lk-btn-light">← Back to Cart</a>
    </div>

    <form action="{{ route('buyer.order.store') }}" method="POST">
        @csrf
        <div class="lk-cart-layout">

            {{-- ── LEFT COLUMN ── --}}
            <div style="display:grid;gap:18px;">

                {{-- Delivery Address --}}
                <div class="lk-card" style="padding:20px;display:grid;gap:16px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                        <h2 style="margin:0;font-size:13px;font-weight:750;">Delivery Address</h2>
                        <a href="{{ route('buyer.account', ['tab' => 'addresses']) }}" style="font-size:10px;font-weight:700;color:var(--lk-red);">Manage Addresses</a>
                    </div>

                    {{-- Saved address picker --}}
                    @if(!empty($savedAddresses))
                    <div style="display:grid;gap:8px;">
                        @foreach($savedAddresses as $addr)
                        <label id="addr-label-{{ $addr['id'] }}" style="display:flex;align-items:flex-start;gap:11px;padding:12px;border:2px solid {{ $addr['default'] ? 'var(--lk-red)' : 'var(--lk-border)' }};border-radius:12px;cursor:pointer;transition:border-color 150ms ease;background:{{ $addr['default'] ? 'var(--lk-red-soft)' : '#fff' }};">
                            <input type="radio" name="saved_address" value="{{ $addr['id'] }}" {{ $addr['default'] ? 'checked' : '' }}
                                   data-addr='@json($addr)'
                                   style="margin-top:2px;accent-color:var(--lk-red);width:14px;height:14px;flex:0 0 14px;"
                                   onchange="lkFillAddress(this)">
                            <div style="display:grid;gap:2px;min-width:0;">
                                <div style="display:flex;align-items:center;gap:7px;">
                                    <strong style="font-size:11px;">{{ $addr['label'] }}</strong>
                                    @if($addr['default'])
                                        <span style="padding:2px 7px;background:var(--lk-red);color:#fff;border-radius:999px;font-size:8px;font-weight:750;">Default</span>
                                    @endif
                                </div>
                                <span style="font-size:10px;color:var(--lk-muted);">{{ $addr['name'] }} · {{ $addr['phone'] }}</span>
                                <span style="font-size:10px;color:var(--lk-muted);">{{ $addr['address'] }}, {{ $addr['city'] }}, {{ $addr['province'] }} {{ $addr['zip'] }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @endif

                    {{-- Editable fields (pre-filled from default address) --}}
                    <details style="border-top:1px solid var(--lk-border);padding-top:14px;">
                        <summary style="font-size:10px;font-weight:700;color:var(--lk-red);cursor:pointer;list-style:none;">Edit delivery details ▾</summary>
                        <div style="display:grid;gap:12px;margin-top:14px;">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                                <div style="display:grid;gap:5px;">
                                    <label style="font-size:10px;font-weight:650;color:var(--lk-muted);">First Name</label>
                                    <input id="ck_first_name" type="text" name="first_name" value="{{ $firstName }}" placeholder="Juan" required
                                           style="height:38px;padding:0 12px;border:1px solid var(--lk-border);border-radius:10px;font-size:11px;outline:0;width:100%;">
                                </div>
                                <div style="display:grid;gap:5px;">
                                    <label style="font-size:10px;font-weight:650;color:var(--lk-muted);">Last Name</label>
                                    <input id="ck_last_name" type="text" name="last_name" value="{{ $lastName }}" placeholder="Dela Cruz" required
                                           style="height:38px;padding:0 12px;border:1px solid var(--lk-border);border-radius:10px;font-size:11px;outline:0;width:100%;">
                                </div>
                            </div>
                            <div style="display:grid;gap:5px;">
                                <label style="font-size:10px;font-weight:650;color:var(--lk-muted);">Phone Number</label>
                                <input id="ck_phone" type="tel" name="phone" value="{{ $userPhone }}" placeholder="09XX XXX XXXX" required
                                       style="height:38px;padding:0 12px;border:1px solid var(--lk-border);border-radius:10px;font-size:11px;outline:0;width:100%;">
                            </div>
                            <div style="display:grid;gap:5px;">
                                <label style="font-size:10px;font-weight:650;color:var(--lk-muted);">Street Address</label>
                                <input id="ck_address" type="text" name="address" value="{{ $defaultAddress['address'] ?? '' }}" placeholder="House no., Street, Barangay" required
                                       style="height:38px;padding:0 12px;border:1px solid var(--lk-border);border-radius:10px;font-size:11px;outline:0;width:100%;">
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                                <div style="display:grid;gap:5px;">
                                    <label style="font-size:10px;font-weight:650;color:var(--lk-muted);">City / Municipality</label>
                                    <input id="ck_city" type="text" name="city" value="{{ $defaultAddress['city'] ?? '' }}" placeholder="Quezon City" required
                                           style="height:38px;padding:0 12px;border:1px solid var(--lk-border);border-radius:10px;font-size:11px;outline:0;width:100%;">
                                </div>
                                <div style="display:grid;gap:5px;">
                                    <label style="font-size:10px;font-weight:650;color:var(--lk-muted);">Province</label>
                                    <input id="ck_province" type="text" name="province" value="{{ $defaultAddress['province'] ?? '' }}" placeholder="Metro Manila"
                                           style="height:38px;padding:0 12px;border:1px solid var(--lk-border);border-radius:10px;font-size:11px;outline:0;width:100%;">
                                </div>
                                <div style="display:grid;gap:5px;">
                                    <label style="font-size:10px;font-weight:650;color:var(--lk-muted);">ZIP Code</label>
                                    <input id="ck_zip" type="text" name="zip" value="{{ $defaultAddress['zip'] ?? '' }}" placeholder="1100"
                                           style="height:38px;padding:0 12px;border:1px solid var(--lk-border);border-radius:10px;font-size:11px;outline:0;width:100%;">
                                </div>
                            </div>
                        </div>
                    </details>
                </div>

                {{-- Payment Method --}}
                <div class="lk-card" style="padding:20px;display:grid;gap:14px;">
                    <h2 style="margin:0;font-size:13px;font-weight:750;">Payment Method</h2>

                    @foreach([
                        ['cod',   'Cash on Delivery',  'Pay when your order arrives.'],
                        ['gcash', 'GCash',             'Pay via GCash e-wallet.'],
                        ['card',  'Credit / Debit Card','Visa, Mastercard accepted.'],
                    ] as [$val, $label, $desc])
                    <label style="display:flex;align-items:center;gap:12px;padding:13px;border:1px solid var(--lk-border);border-radius:12px;cursor:pointer;transition:border-color 150ms ease;"
                           onmouseover="this.style.borderColor='var(--lk-border-strong)'" onmouseout="this.style.borderColor='var(--lk-border)'">
                        <input type="radio" name="payment_method" value="{{ $val }}" {{ $val==='cod' ? 'checked' : '' }}
                               style="accent-color:var(--lk-red);width:15px;height:15px;flex:0 0 15px;">
                        <div style="display:grid;gap:2px;">
                            <strong style="font-size:11px;">{{ $label }}</strong>
                            <span style="font-size:9px;color:var(--lk-muted);">{{ $desc }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>

                {{-- Order Notes --}}
                <div class="lk-card" style="padding:20px;display:grid;gap:10px;">
                    <h2 style="margin:0;font-size:13px;font-weight:750;">Order Notes <span style="font-weight:400;color:var(--lk-muted);font-size:10px;">(optional)</span></h2>
                    <textarea name="notes" rows="3" placeholder="Special instructions for your order or delivery…"
                              style="padding:10px 12px;border:1px solid var(--lk-border);border-radius:10px;font-size:11px;outline:0;resize:vertical;width:100%;font-family:inherit;"></textarea>
                </div>
            </div>

            {{-- ── ORDER SUMMARY ── --}}
            <aside class="lk-cart-summary">
                <div class="lk-card lk-summary-card">
                    <h3>Order Summary</h3>

                    {{-- Items list --}}
                    <div style="display:grid;gap:10px;padding-bottom:12px;border-bottom:1px solid var(--lk-border);">
                        @foreach($cartItems as $item)
                        @php $price = data_get($item,'price',0); $qty = data_get($item,'quantity',1); @endphp
                        <div style="display:flex;gap:10px;align-items:center;">
                            <div style="width:40px;height:40px;flex:0 0 40px;border-radius:9px;overflow:hidden;background:#f5eee4;display:grid;place-items:center;">
                                @if(data_get($item,'image'))
                                    <img src="{{ data_get($item,'image') }}" alt="" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:#c9b49a;"><path d="M4 5h16v14H4z"/><path d="m4 15 4-4 4 4 3-3 5 5"/></svg>
                                @endif
                            </div>
                            <div style="flex:1;min-width:0;">
                                <p style="margin:0;font-size:10px;font-weight:650;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ data_get($item,'name') }}</p>
                                <p style="margin:2px 0 0;font-size:9px;color:var(--lk-muted);">× {{ $qty }}</p>
                            </div>
                            <strong style="font-size:10px;flex:0 0 auto;">₱{{ number_format($price * $qty, 2) }}</strong>
                        </div>
                        @endforeach
                    </div>

                    <div style="display:flex;flex-direction:column;gap:9px;">
                        <div class="lk-summary-row">
                            <span>Subtotal</span>
                            <span>₱{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="lk-summary-row">
                            <span>Shipping fee</span>
                            <span>₱{{ number_format($shipping, 2) }}</span>
                        </div>
                        @if($discount > 0)
                        <div class="lk-summary-row" style="color:#16a34a;">
                            <span>Discount</span>
                            <span>−₱{{ number_format($discount, 2) }}</span>
                        </div>
                        @endif
                    </div>

                    <hr class="lk-summary-divider">

                    <div class="lk-summary-row lk-summary-total">
                        <span>Total</span>
                        <span>₱{{ number_format($total, 2) }}</span>
                    </div>

                    <button type="submit" class="lk-btn lk-btn-red lk-btn-full">
                        Place Order
                    </button>

                    <p style="margin:0;text-align:center;font-size:9px;color:var(--lk-muted);">
                        By placing your order you agree to our
                        <a href="#" style="color:var(--lk-red);">Terms of Service</a>.
                    </p>
                </div>

                <div style="margin-top:12px;padding:14px;background:#fff;border:1px solid var(--lk-border);border-radius:14px;">
                    <p style="margin:0 0 8px;font-size:10px;font-weight:700;color:var(--lk-ink);">Buyer Protection</p>
                    @foreach(['✓ Secure checkout','✓ Easy returns & refunds','✓ Verified local sellers'] as $pt)
                        <p style="margin:0;font-size:9px;color:var(--lk-muted);line-height:2;">{{ $pt }}</p>
                    @endforeach
                </div>
            </aside>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function lkFillAddress(radio) {
    var addr = JSON.parse(radio.getAttribute('data-addr'));
    var nameParts = (addr.name || '').split(' ');
    document.getElementById('ck_first_name').value = nameParts[0] || '';
    document.getElementById('ck_last_name').value  = nameParts.slice(1).join(' ') || '';
    document.getElementById('ck_phone').value    = addr.phone    || '';
    document.getElementById('ck_address').value  = addr.address  || '';
    document.getElementById('ck_city').value     = addr.city     || '';
    document.getElementById('ck_province').value = addr.province || '';
    document.getElementById('ck_zip').value      = addr.zip      || '';
    /* update card highlight */
    document.querySelectorAll('[name=saved_address]').forEach(function(r) {
        var lbl = r.closest('label');
        lbl.style.borderColor = r.checked ? 'var(--lk-red)' : 'var(--lk-border)';
        lbl.style.background  = r.checked ? 'var(--lk-red-soft)' : '#fff';
    });
}
</script>
@endpush
