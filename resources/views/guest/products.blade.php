@extends('layouts.guest')

@section('title', 'Products — LIKHAE Marketplace')

@section('content')
    <x-buyer.product-catalog :products="$buyerProducts" :guest="true" title="Browse Products" subtitle="Find quality products from trusted local sellers." />
@endsection