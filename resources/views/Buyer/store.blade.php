@extends('layouts.buyer')

@section('title', 'Store')
@section('active', 'products')
@section('subtitle', 'Browse products from this trusted seller.')

@section('content')
    <x-buyer.storefront :seller="$seller" :products="$buyerProducts" />
@endsection
