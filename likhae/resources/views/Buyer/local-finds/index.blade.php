@include('Buyer.product-data')
<x-buyer.layout title="Local Finds"><div class="b-page"><div class="b-container">
<x-buyer.breadcrumbs :items="[['label'=>'Local Finds','url'=>null]]" />
<div class="b-section-head"><div><h1 class="b-title">Local Finds</h1><p class="b-muted">Discover products from sellers across the Philippines.</p></div></div>
<div class="b-product-grid">@foreach($buyerProducts as $product)<x-buyer.product-card :product="$product" />@endforeach</div>
</div></div></x-buyer.layout>
