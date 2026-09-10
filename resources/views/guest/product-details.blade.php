@extends('layouts.guest')

@section('title', data_get($product, 'name', 'Product Details'))

@section('content')
@php
    $slug = data_get($product, 'slug', data_get($product, 'id', 'product'));
    $name = (string) data_get($product, 'name', 'Product');
    $category = (string) data_get($product, 'category', 'Local Find');
    $seller = (string) data_get($product, 'seller', 'LIKHAE Seller');
    $sellerAvatar = data_get($product, 'seller_avatar', 'https://ui-avatars.com/api/?name='.urlencode($seller).'&background=7f1d1d&color=fff');
    $images = collect(data_get($product, 'gallery', []))->filter()->values();
    if ($images->isEmpty()) $images = collect([data_get($product, 'image')]);
    $specs = collect(data_get($product, 'specs', []));
    $variations = collect(data_get($product, 'variations', []));
    $related = collect($buyerProducts ?? [])->where('category', $category)->where('slug', '!=', $slug)->take(4);
    if ($related->count() < 4) $related = collect($buyerProducts ?? [])->where('slug', '!=', $slug)->take(4);
@endphp

<div class="lk-page">
    <div class="mb-4 flex flex-wrap items-center gap-2 text-xs text-stone-500">
        <a class="hover:text-red-800" href="{{ \Illuminate\Support\Facades\Route::has('guest.home') ? route('guest.home') : route('products') }}">Products</a>
        <span>/</span><span>{{ $category }}</span><span>/</span><strong class="text-stone-700">{{ $name }}</strong>
    </div>

    <section class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_minmax(360px,.9fr)]">
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm" data-product-gallery>
            <div class="aspect-square overflow-hidden rounded-xl bg-stone-50">
                <img class="h-full w-full object-cover" src="{{ $images->first() }}" alt="{{ $name }}" data-gallery-main>
            </div>
            @if($images->count() > 1)
                <div class="mt-3 grid grid-cols-5 gap-2">
                    @foreach($images->take(5) as $index => $galleryImage)
                        <button type="button" class="overflow-hidden rounded-lg border-2 {{ $index === 0 ? 'border-red-800' : 'border-transparent' }} bg-stone-50" data-gallery-thumb data-image="{{ $galleryImage }}" aria-label="View image {{ $index + 1 }}">
                            <img class="aspect-square h-full w-full object-cover" src="{{ $galleryImage }}" alt="{{ $name }} view {{ $index + 1 }}">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-red-50 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-red-800">{{ $category }}</span>
            </div>
            <h1 class="mt-3 text-2xl font-bold leading-tight text-stone-950 sm:text-3xl">{{ $name }}</h1>

            <div class="mt-5 space-y-4">
                @foreach($variations as $variationName => $options)
                    <div>
                        <span class="mb-2 block text-xs font-semibold text-stone-700">{{ $variationName }}</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach((array) $options as $option)
                                <span class="rounded-lg border border-stone-300 px-3 py-2 text-xs font-medium text-stone-700">{{ $option }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 grid gap-2 rounded-xl border border-stone-200 p-4 text-xs text-stone-600 sm:grid-cols-2">
                <div><strong class="block text-stone-800">Delivery</strong>{{ data_get($product, 'shipping', 'Metro Manila: 1-3 days') }}</div>
                <div><strong class="block text-stone-800">Buyer protection</strong>Payment held until receipt</div>
            </div>
        </div>
    </section>

    <section class="mt-5 flex flex-col gap-4 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center">
        <img class="h-16 w-16 rounded-xl object-cover" src="{{ $sellerAvatar }}" alt="{{ $seller }}">
        <div class="min-w-0 flex-1"><span class="text-[10px] font-bold uppercase tracking-widest text-red-800">Official seller</span><h2 class="truncate text-lg font-bold text-stone-900">{{ $seller }}</h2><p class="text-xs text-stone-500">{{ data_get($product, 'location', 'Philippines') }}</p></div>
    </section>

    <div class="mt-5 grid gap-5 lg:grid-cols-[minmax(0,1fr)_360px]">
        <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-lg font-bold text-stone-900">Description</h2>
            <div class="mt-3 whitespace-pre-line text-sm leading-7 text-stone-600">{{ data_get($product, 'description', 'A carefully selected product from a trusted LIKHAE seller. Built for everyday use and covered by marketplace buyer protection.') }}</div>
        </section>
        <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-lg font-bold text-stone-900">Specifications</h2>
            <dl class="mt-3 divide-y divide-stone-100 text-xs">@forelse($specs as $label => $value)<div class="grid grid-cols-[110px_1fr] gap-3 py-2.5"><dt class="text-stone-500">{{ $label }}</dt><dd class="font-medium text-stone-800">{{ $value }}</dd></div>@empty<div class="py-3 text-stone-500">Seller has not added specifications yet.</div>@endforelse</dl>
        </section>
    </div>

    @if($related->isNotEmpty())
        <section class="lk-section"><div class="lk-section-head"><div><span class="lk-kicker">Keep exploring</span><h2>Related Products</h2><p>More finds you may like.</p></div></div><div class="lk-product-grid">@foreach($related as $relatedProduct) @include('guest.partials.product-card', ['product' => $relatedProduct]) @endforeach</div></section>
    @endif
</div>
@endsection
