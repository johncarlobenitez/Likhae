@extends('layouts.buyer')
@section('title', 'Seller Shop — LIKHAE')
@section('active', 'products')

@section('content')
@php
    /*
    |--------------------------------------------------------------------------
    | Front-end sample data
    | Replace with real $shop / $shopProducts from controller when ready.
    |--------------------------------------------------------------------------
    */

    // Derive a display name from the URL slug (e.g. "metro-finds-ph" → "Metro Finds PH")
    $slug = $sellerSlug ?? request()->route('seller') ?? 'sample-shop';
    $slugDisplay = collect(explode('-', $slug))->map(fn($w) => ucfirst($w))->join(' ');

    $shop = $shop ?? [
        'name'          => $slugDisplay,
        'slug'          => $slug,
        'avatar'        => null,          // URL or null → initials fallback
        'cover'         => null,          // URL or null → gradient fallback
        'tagline'       => 'Quality products from a trusted local seller.',
        'rating'        => 4.9,
        'reviews'       => 312,
        'followers'     => 1840,
        'sales'         => 2600,
        'response_rate' => 98,
        'response_time' => '< 1 hour',
        'joined'        => 'January 2024',
        'location'      => 'Metro Manila, Philippines',
        'verified'      => true,
    ];

    // Products belonging to this shop — fall back to shared $buyerProducts
    if (isset($buyerProducts) && method_exists($buyerProducts, 'items')) {
        $allProducts = collect($buyerProducts->items());
    } else {
        $allProducts = collect($buyerProducts ?? []);
    }

    // In production, filter by seller. Frontend demo: use all products.
    $shopProducts = $allProducts;

    $activeTab  = request('tab', 'products');
    $validTabs  = ['home', 'products', 'reviews', 'about'];
    $activeTab  = in_array($activeTab, $validTabs) ? $activeTab : 'products';

    $initial = strtoupper(mb_substr(data_get($shop, 'name', 'S'), 0, 1));

    $stars = fn(float $r): string =>
        str_repeat('★', (int) round($r)) .
        str_repeat('☆', 5 - (int) round($r));
@endphp

<div class="lk-page" style="padding-top:0;">

    {{-- ══════════════════════════════════════════
         SHOP COVER + HEADER
    ══════════════════════════════════════════ --}}
    <section style="
        position:relative;
        border-radius:0 0 24px 24px;
        overflow:hidden;
        margin:-28px -28px 0;
    ">
        {{-- Cover image / gradient --}}
        <div style="
            height:200px;
            background: {{ data_get($shop,'cover') ? 'url('.data_get($shop,'cover').') center/cover no-repeat' : 'linear-gradient(135deg,#8f1719 0%,#c0392b 50%,#e05840 100%)' }};
        "></div>

        {{-- Shop identity bar --}}
        <div style="
            background:#fff;
            padding:0 32px 20px;
            display:flex;
            align-items:flex-end;
            gap:20px;
            flex-wrap:wrap;
            border-bottom:1px solid var(--lk-border);
        ">
            {{-- Avatar --}}
            <div style="
                width:88px;height:88px;
                border-radius:20px;
                border:4px solid #fff;
                background:{{ data_get($shop,'cover') ? '#fff' : 'var(--lk-red-soft)' }};
                overflow:hidden;
                display:grid;place-items:center;
                margin-top:-44px;
                flex-shrink:0;
                box-shadow:0 4px 16px rgba(0,0,0,.12);
            ">
                @if(data_get($shop,'avatar'))
                    <img src="{{ data_get($shop,'avatar') }}" alt="{{ data_get($shop,'name') }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <span style="font-size:32px;font-weight:800;color:var(--lk-red);">{{ $initial }}</span>
                @endif
            </div>

            {{-- Name + meta --}}
            <div style="flex:1;min-width:0;padding-bottom:4px;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-top:12px;">
                    <h1 style="margin:0;font-size:20px;font-weight:800;color:var(--lk-ink);">
                        {{ data_get($shop,'name') }}
                    </h1>
                    @if(data_get($shop,'verified'))
                        <span style="
                            display:inline-flex;align-items:center;gap:4px;
                            padding:3px 9px;border-radius:999px;
                            background:var(--lk-red-soft);color:var(--lk-red);
                            font-size:10px;font-weight:700;
                        ">✓ Verified Seller</span>
                    @endif
                </div>

                <div style="display:flex;flex-wrap:wrap;gap:16px;margin-top:8px;font-size:11px;color:var(--lk-muted);">
                    <span style="color:#f59e0b;">
                        {!! $stars(data_get($shop,'rating',5)) !!}
                        <strong style="color:var(--lk-ink);margin-left:3px;">{{ number_format(data_get($shop,'rating',4.9),1) }}</strong>
                        <span>({{ number_format(data_get($shop,'reviews',0)) }} reviews)</span>
                    </span>
                    <span>{{ number_format(data_get($shop,'followers',0)) }} Followers</span>
                    <span>{{ number_format(data_get($shop,'sales',0)) }}+ Sales</span>
                    <span>📍 {{ data_get($shop,'location') }}</span>
                </div>
            </div>

            {{-- Action buttons --}}
            <div style="display:flex;gap:10px;padding-bottom:4px;flex-shrink:0;">
                <a href="{{ route('buyer.messages') }}" class="lk-btn lk-btn-light lk-btn-sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="flex-shrink:0;">
                        <path d="M4 5h16v11H8l-4 4z"/>
                    </svg>
                    Chat Seller
                </a>
                <button type="button" class="lk-btn lk-btn-sm" id="followBtn"
                        style="background:var(--lk-red);color:#fff;border-color:var(--lk-red);"
                        onclick="toggleFollow(this)">
                    + Follow
                </button>
            </div>
        </div>

        {{-- Tabs --}}
        <div style="background:#fff;display:flex;gap:0;border-bottom:1px solid var(--lk-border);padding:0 32px;overflow-x:auto;">
            @foreach(['home'=>'Home','products'=>'Products','reviews'=>'Reviews','about'=>'About'] as $tab=>$label)
                <a href="{{ route('buyer.shop', ['seller'=>$slug,'tab'=>$tab]) }}"
                   style="
                       display:inline-flex;align-items:center;padding:14px 18px;
                       font-size:12px;font-weight:600;white-space:nowrap;
                       color:{{ $activeTab===$tab ? 'var(--lk-red)' : 'var(--lk-muted)' }};
                       border-bottom:2px solid {{ $activeTab===$tab ? 'var(--lk-red)' : 'transparent' }};
                       transition:color .15s;
                   ">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </section>

    {{-- ══════════════════════════════════════════
         TAB: HOME
    ══════════════════════════════════════════ --}}
    @if($activeTab === 'home')
    <div style="display:grid;gap:24px;margin-top:24px;">

        {{-- Stats strip --}}
        <div style="
            display:grid;grid-template-columns:repeat(4,1fr);gap:1px;
            background:var(--lk-border);border-radius:16px;overflow:hidden;
            border:1px solid var(--lk-border);
        ">
            @foreach([
                ['Seller Rating',  number_format(data_get($shop,'rating',4.9),1).' ★'],
                ['Response Rate',  data_get($shop,'response_rate',98).'%'],
                ['Response Time',  data_get($shop,'response_time','< 1 hour')],
                ['Member Since',   data_get($shop,'joined','2024')],
            ] as [$label,$value])
                <div style="background:#fff;padding:16px 20px;text-align:center;">
                    <div style="font-size:16px;font-weight:800;color:var(--lk-ink);">{{ $value }}</div>
                    <div style="font-size:10px;color:var(--lk-muted);margin-top:3px;">{{ $label }}</div>
                </div>
            @endforeach
        </div>

        {{-- Featured products (first 4) --}}
        <div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                <h2 style="margin:0;font-size:16px;font-weight:700;">Featured Products</h2>
                <a href="{{ route('buyer.shop',['seller'=>$slug,'tab'=>'products']) }}"
                   style="font-size:11px;font-weight:700;color:var(--lk-red);">View All →</a>
            </div>
            <div class="lk-product-grid">
                @foreach($shopProducts->take(4) as $p)
                    <x-buyer.product-card :product="$p"/>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════
         TAB: PRODUCTS
    ══════════════════════════════════════════ --}}
    @if($activeTab === 'products')
    <div style="margin-top:24px;">

        {{-- Toolbar --}}
        <div style="
            display:flex;align-items:center;justify-content:space-between;
            background:#fff;border:1px solid var(--lk-border);border-radius:14px;
            padding:10px 16px;margin-bottom:16px;gap:12px;flex-wrap:wrap;
        ">
            <span style="font-size:12px;color:var(--lk-muted);">
                <strong style="color:var(--lk-ink);">{{ $shopProducts->count() }}</strong> products
            </span>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                @foreach(['All','New Arrivals','Best Sellers','On Sale'] as $f)
                    <button class="lk-variant {{ $loop->first ? 'is-active':'' }}"
                            style="height:28px;font-size:10px;padding:0 10px;"
                            onclick="shopFilter(this)">{{ $f }}</button>
                @endforeach
            </div>
        </div>

        @if($shopProducts->isNotEmpty())
            <div class="lk-product-grid">
                @foreach($shopProducts as $p)
                    <x-buyer.product-card :product="$p"/>
                @endforeach
            </div>
        @else
            <div style="
                text-align:center;padding:60px 40px;background:#fff;
                border:2px dashed var(--lk-border);border-radius:20px;
            ">
                <p style="margin:0;color:var(--lk-muted);">No products available yet.</p>
            </div>
        @endif
    </div>
    @endif

    {{-- ══════════════════════════════════════════
         TAB: REVIEWS
    ══════════════════════════════════════════ --}}
    @if($activeTab === 'reviews')
    @php
        $sampleReviews = [
            ['initial'=>'J','name'=>'Juan D.','rating'=>5,'date'=>'Sep 1, 2026','product'=>'Premium Wireless Headphones','text'=>'Great shop! Fast shipping and items were well-packed. Would definitely order again.','verified'=>true],
            ['initial'=>'M','name'=>'Maria S.','rating'=>5,'date'=>'Aug 28, 2026','product'=>'Classic Everyday Backpack','text'=>'Excellent seller. The bag is exactly as described and arrived in perfect condition.','verified'=>true],
            ['initial'=>'R','name'=>'Ramon T.','rating'=>4,'date'=>'Aug 20, 2026','product'=>'Lightweight Running Shoes','text'=>'Good quality shoes. Delivery was a bit slow but the seller was communicative throughout.','verified'=>true],
            ['initial'=>'A','name'=>'Ana G.','rating'=>5,'date'=>'Aug 15, 2026','product'=>'Everyday Smart Watch','text'=>'The watch is amazing quality for the price. Seller responded quickly to all my questions.','verified'=>false],
        ];
    @endphp
    <div style="margin-top:24px;display:grid;gap:16px;">

        {{-- Rating summary --}}
        <div style="background:#fff;border:1px solid var(--lk-border);border-radius:16px;padding:24px;display:grid;grid-template-columns:auto 1fr;gap:24px;align-items:center;">
            <div style="text-align:center;padding-right:24px;border-right:1px solid var(--lk-border);">
                <div style="font-size:48px;font-weight:800;color:var(--lk-ink);line-height:1;">
                    {{ number_format(data_get($shop,'rating',4.9),1) }}
                </div>
                <div style="color:#f59e0b;font-size:18px;margin-top:4px;">★★★★★</div>
                <div style="font-size:11px;color:var(--lk-muted);margin-top:4px;">
                    {{ number_format(data_get($shop,'reviews',0)) }} reviews
                </div>
            </div>
            <div style="display:grid;gap:8px;">
                @foreach([5=>85, 4=>10, 3=>3, 2=>1, 1=>1] as $star=>$pct)
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span style="font-size:11px;color:var(--lk-muted);width:36px;text-align:right;">{{ $star }} ★</span>
                        <div style="flex:1;height:8px;background:#f5f3ef;border-radius:999px;overflow:hidden;">
                            <div style="height:100%;width:{{ $pct }}%;background:{{ $star>=4 ? '#f59e0b' : ($star===3 ? '#fbbf24' : '#e5ded6') }};border-radius:999px;"></div>
                        </div>
                        <span style="font-size:11px;color:var(--lk-muted);width:28px;">{{ $pct }}%</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Review cards --}}
        @foreach($sampleReviews as $review)
            <div style="background:#fff;border:1px solid var(--lk-border);border-radius:16px;padding:20px;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="
                            width:40px;height:40px;border-radius:50%;
                            background:var(--lk-red-soft);color:var(--lk-red);
                            display:grid;place-items:center;font-weight:700;font-size:15px;flex-shrink:0;
                        ">{{ $review['initial'] }}</div>
                        <div>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <strong style="font-size:13px;">{{ $review['name'] }}</strong>
                                @if($review['verified'])
                                    <span style="font-size:9px;background:#f0fdf4;color:#16a34a;padding:2px 6px;border-radius:999px;font-weight:700;">✓ Verified</span>
                                @endif
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;margin-top:3px;">
                                <span style="color:#f59e0b;font-size:12px;">
                                    {!! $stars($review['rating']) !!}
                                </span>
                                <span style="font-size:10px;color:var(--lk-muted);">{{ $review['date'] }}</span>
                            </div>
                        </div>
                    </div>
                    <span style="font-size:10px;color:var(--lk-muted);background:#f5f3ef;padding:3px 8px;border-radius:999px;">
                        {{ $review['product'] }}
                    </span>
                </div>
                <p style="margin:12px 0 0;font-size:13px;color:#57534e;line-height:1.7;">{{ $review['text'] }}</p>
                <div style="display:flex;gap:10px;margin-top:12px;">
                    <button type="button"
                            onclick="this.textContent=this.textContent.includes('(0)')?'👍 Helpful (1)':'👍 Helpful (0)'"
                            style="font-size:10px;color:var(--lk-muted);background:#f5f3ef;border:0;border-radius:999px;padding:4px 10px;cursor:pointer;">
                        👍 Helpful (0)
                    </button>
                </div>
            </div>
        @endforeach
    </div>
    @endif

    {{-- ══════════════════════════════════════════
         TAB: ABOUT
    ══════════════════════════════════════════ --}}
    @if($activeTab === 'about')
    <div style="margin-top:24px;display:grid;gap:16px;max-width:720px;">
        <div style="background:#fff;border:1px solid var(--lk-border);border-radius:16px;padding:24px;display:grid;gap:20px;">

            <div>
                <h2 style="margin:0 0 10px;font-size:15px;font-weight:700;">About {{ data_get($shop,'name') }}</h2>
                <p style="margin:0;font-size:13px;color:#57534e;line-height:1.8;">
                    {{ data_get($shop,'tagline','Trusted local seller offering quality products with fast delivery and excellent customer service.') }}
                    We are committed to providing buyers with the best shopping experience on LIKHAE Marketplace.
                </p>
            </div>

            <hr style="border:0;border-top:1px solid var(--lk-border);">

            <div style="display:grid;gap:12px;">
                @foreach([
                    ['📍 Location',      data_get($shop,'location','Metro Manila')],
                    ['📅 Member Since',  data_get($shop,'joined','2024')],
                    ['⚡ Response Rate', data_get($shop,'response_rate',98).'%'],
                    ['⏱ Response Time',  data_get($shop,'response_time','< 1 hour')],
                    ['⭐ Rating',        number_format(data_get($shop,'rating',4.9),1).' out of 5'],
                    ['👥 Followers',     number_format(data_get($shop,'followers',0)).' followers'],
                ] as [$label, $value])
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 0;border-bottom:1px solid var(--lk-border);">
                        <span style="font-size:12px;color:var(--lk-muted);">{{ $label }}</span>
                        <strong style="font-size:12px;color:var(--lk-ink);">{{ $value }}</strong>
                    </div>
                @endforeach
            </div>

            <a href="{{ route('buyer.messages') }}" class="lk-btn lk-btn-red" style="width:fit-content;">
                Chat with Seller
            </a>
        </div>
    </div>
    @endif

</div>

<script>
function toggleFollow(btn) {
    const following = btn.dataset.following === '1';
    if (following) {
        btn.textContent = '+ Follow';
        btn.style.background = 'var(--lk-red)';
        btn.style.color = '#fff';
        btn.style.borderColor = 'var(--lk-red)';
        btn.dataset.following = '0';
    } else {
        btn.textContent = '✓ Following';
        btn.style.background = '#fff';
        btn.style.color = 'var(--lk-red)';
        btn.style.borderColor = 'var(--lk-red)';
        btn.dataset.following = '1';
    }
}

function shopFilter(btn) {
    document.querySelectorAll('.lk-variant[onclick^="shopFilter"]').forEach(b => b.classList.remove('is-active'));
    btn.classList.add('is-active');
}
</script>

@endsection
