@extends('layouts.buyer')

@section('title', 'Product Details')
@section('active', 'products')
@section('subtitle', 'Review the product, seller, specifications, and ratings.')

@section('content')
    <x-buyer.product-detail :product="$product" :products="$buyerProducts" />
@endsection
