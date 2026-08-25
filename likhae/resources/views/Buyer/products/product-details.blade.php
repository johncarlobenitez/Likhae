@php
$slug = $slug ?? 'linen-lounge-set';
@endphp
<x-marketplace.layout title="Product Details" :buyer="true">
    <x-marketplace.product-detail :buyer="true" :slug="$slug" />
</x-marketplace.layout>
