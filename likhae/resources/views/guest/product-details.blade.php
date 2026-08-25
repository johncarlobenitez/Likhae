@php
$slug = $slug ?? 'linen-lounge-set';
@endphp
<x-marketplace.layout title="Product Details" :buyer="false">
    <x-marketplace.product-detail :buyer="false" :slug="$slug" />
</x-marketplace.layout>
