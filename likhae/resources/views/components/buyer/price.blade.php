@props(['price'=>0,'oldPrice'=>null,'discount'=>null])
<div class="b-price"><span class="b-price-current">₱{{ number_format((float)$price,2) }}</span>@if($oldPrice)<span class="b-price-old">₱{{ number_format((float)$oldPrice,2) }}</span>@endif @if($discount)<span class="b-discount">{{ $discount }}% OFF</span>@endif</div>
