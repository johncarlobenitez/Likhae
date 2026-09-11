@extends('layouts.buyer')

@section('title', 'Products')
@section('active', 'products')
@section('subtitle', 'Find products from trusted local sellers.')

@section('content')
    <x-buyer.product-catalog :products="$buyerProducts" />
@endsection
