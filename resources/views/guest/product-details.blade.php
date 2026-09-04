@extends('layouts.guest')

@section('title', data_get($product, 'name', 'Product Details'))

@section('content')
    <x-buyer.product-detail :product="$product" :products="$buyerProducts" :guest="true" />
@endsection
