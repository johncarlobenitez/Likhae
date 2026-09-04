@extends('layouts.guest')

@section('title', isset($storeMode) ? data_get($seller, 'name', 'Store') : 'Marketplace')

@section('content')
    @if(isset($storeMode) && $storeMode)
        <x-buyer.storefront :seller="$seller" :products="$buyerProducts" :guest="true" />
    @else
        <x-buyer.product-catalog :products="$buyerProducts" :guest="true" title="Explore LIKHAE" subtitle="Browse products and stores. Sign in only when you are ready to buy." />
    @endif
@endsection
