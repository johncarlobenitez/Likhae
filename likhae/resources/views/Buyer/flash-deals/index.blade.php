@include('Buyer.product-data')
<x-buyer.layout title="Flash Deals"><div class="b-page"><div class="b-container">
<x-buyer.breadcrumbs :items="[['label'=>'Flash Deals','url'=>null]]" />
<div class="b-card b-deal-banner"><div><small>FLASH DEALS</small><h1 style="margin:5px 0 0">Limited-time marketplace prices</h1></div><div class="b-countdown"><span>06h</span><span>24m</span><span>18s</span></div></div>
<div class="b-product-grid" style="margin-top:18px">@foreach(array_slice($buyerProducts,0,8) as $product)<x-buyer.product-card :product="$product" />@endforeach</div>
</div></div></x-buyer.layout>
