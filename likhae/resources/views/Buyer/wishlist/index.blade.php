@include('Buyer.product-data')
<x-buyer.layout title="Wishlist"><div class="b-page" data-buyer-wishlist><div class="b-container">
<x-buyer.breadcrumbs :items="[['label'=>'Wishlist','url'=>null]]" /><div class="b-section-head"><div><h1 class="b-title">Saved Products</h1><p class="b-muted">Products you want to revisit later.</p></div></div>
<div class="b-product-grid">@foreach(array_slice($buyerProducts,0,4) as $product)<div data-wishlist-wrap><x-buyer.product-card :product="$product" /><div class="b-wishlist-actions"><a class="b-btn b-btn-secondary" href="{{ route('buyer.product-details',$product['slug']) }}">View Product</a><button class="b-btn b-btn-danger" data-wishlist-remove="{{ $product['id'] }}">Remove</button></div></div>@endforeach</div>
</div></div></x-buyer.layout>
