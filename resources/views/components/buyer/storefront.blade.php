@props(['seller', 'products' => collect(), 'guest' => false])

@php
    $sellerSlug = data_get($seller, 'slug', 'store');
    $sellerName = data_get($seller, 'name', 'LIKHAE Store');
    $storeProducts = collect($products)->filter(function ($product) use ($sellerSlug) {
        $slug = data_get($product, 'seller_slug', \Illuminate\Support\Str::slug((string) data_get($product, 'seller', '')));
        return $slug === $sellerSlug;
    })->values();
    $query = mb_strtolower(trim((string) request('q', '')));
    if ($query) $storeProducts = $storeProducts->filter(fn($product) => str_contains(mb_strtolower(data_get($product, 'name').' '.data_get($product, 'category')), $query))->values();
    $searchAction = $guest ? route('home') : route('buyer.shop', ['seller' => $sellerSlug]);
@endphp

<div class="lk-page">
    <section class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="h-24 bg-gradient-to-r from-red-950 via-red-900 to-stone-900"></div>
        <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-end sm:p-6">
            <img class="-mt-14 h-24 w-24 rounded-2xl border-4 border-white bg-white object-cover shadow-sm" src="{{ data_get($seller, 'avatar') }}" alt="{{ $sellerName }}">
            <div class="min-w-0 flex-1"><span class="text-[10px] font-bold uppercase tracking-widest text-red-800">Verified LIKHAE store</span><h1 class="truncate text-2xl font-bold text-stone-950">{{ $sellerName }}</h1><p class="mt-1 text-xs text-stone-500">{{ data_get($seller, 'location') }} · Joined {{ data_get($seller, 'joined') }}</p></div>
            <div class="flex flex-wrap gap-2">
                <button type="button" class="lk-btn lk-btn-light" @if($guest) data-auth-required data-auth-message="Sign in to follow this store." @else data-demo-action="Store followed." @endif>+ Follow</button>
                @if($guest)
                    <button type="button" class="lk-btn lk-btn-red" data-auth-required data-auth-message="Sign in to message this store.">Message Store</button>
                @else
                    <a href="{{ route('buyer.messages', ['seller' => $sellerSlug]) }}" class="lk-btn lk-btn-red">Message Store</a>
                @endif
            </div>
        </div>
        <div class="grid grid-cols-2 border-t border-stone-100 sm:grid-cols-5">
            @foreach([['Products',$storeProducts->count()],['Rating',data_get($seller,'rating').' / 5'],['Followers',data_get($seller,'followers')],['Response',data_get($seller,'response')],['Fulfillment',data_get($seller,'fulfillment')]] as [$label,$value])
                <div class="border-b border-r border-stone-100 px-4 py-3 text-center sm:border-b-0"><strong class="block text-sm text-stone-900">{{ $value }}</strong><span class="text-[10px] uppercase tracking-wide text-stone-400">{{ $label }}</span></div>
            @endforeach
        </div>
    </section>

    <section class="mt-5 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"><div><span class="lk-kicker">Store collection</span><h2 class="text-xl font-bold text-stone-900">Products by {{ $sellerName }}</h2><p class="text-xs text-stone-500">Only products published by this seller are shown.</p></div><form method="GET" action="{{ $searchAction }}" class="flex min-w-0 gap-2"><input name="q" value="{{ request('q') }}" class="min-w-0 rounded-xl border border-stone-300 px-3 py-2 text-xs outline-none focus:border-red-800" placeholder="Search this store"><button class="lk-btn lk-btn-light" type="submit">Search</button></form></div>
        @if($storeProducts->isNotEmpty())<div class="lk-product-grid mt-5">@foreach($storeProducts as $product)<x-buyer.product-card :product="$product" :guest="$guest" />@endforeach</div>@else<div class="mt-5"><x-buyer.empty-state title="No store products found" message="Try another keyword or browse the full marketplace." /></div>@endif
    </section>

    <section class="mt-5 grid gap-4 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm md:grid-cols-[1fr_300px]"><div><h2 class="text-base font-bold text-stone-900">About this store</h2><p class="mt-2 text-sm leading-6 text-stone-600">{{ data_get($seller, 'description') }}</p></div><dl class="space-y-2 text-xs"><div class="flex justify-between gap-3"><dt class="text-stone-500">Location</dt><dd class="font-medium text-stone-800">{{ data_get($seller, 'location') }}</dd></div><div class="flex justify-between gap-3"><dt class="text-stone-500">Business hours</dt><dd class="font-medium text-stone-800">{{ data_get($seller, 'hours') }}</dd></div><div class="flex justify-between gap-3"><dt class="text-stone-500">Response time</dt><dd class="font-medium text-stone-800">{{ data_get($seller, 'response') }}</dd></div></dl></section>
</div>
