@props(['product'=>[]])
@php
$id=$product['id']??'product';$name=$product['name']??'Marketplace Product';$slug=$product['slug']??$id;$image=$product['image']??'/images/buyer/products/headphones.svg';$seller=$product['seller']??'LIKHAE Seller';$category=$product['category']??'Others';$price=(float)($product['price']??0);$oldPrice=isset($product['old_price'])?(float)$product['old_price']:null;$discount=$product['discount']??null;$rating=$product['rating']??4.8;$reviews=$product['reviews']??0;$sold=$product['sold']??0;
@endphp
<article class="b-product-card" data-buyer-product-card data-category="{{ $category }}" data-price="{{ $price }}" data-rating="{{ $rating }}" data-sold="{{ $sold }}" data-search="{{ strtolower($name.' '.$seller.' '.$category) }}">
<a class="b-product-image" href="{{ route('buyer.product-details',$slug) }}"><img src="{{ asset(ltrim($image,'/')) }}" alt="{{ $name }}" loading="lazy"><button class="b-product-wishlist" type="button" data-buyer-wishlist-toggle data-product-id="{{ $id }}">♡</button></a>
<div class="b-product-body"><a href="{{ route('buyer.product-details',$slug) }}"><h3 class="b-product-name">{{ $name }}</h3></a><div class="b-product-seller">{{ $seller }}</div><x-buyer.price :price="$price" :old-price="$oldPrice" :discount="$discount" /><div class="b-product-meta"><span>★ {{ $rating }} ({{ $reviews }})</span><span>{{ $sold }} sold</span></div></div>
</article>
