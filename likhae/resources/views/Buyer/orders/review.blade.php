@php $id=$id??request()->route('id')??'001198'; @endphp
<x-buyer.layout title="Rate Product"><div class="b-page"><div class="b-container" style="max-width:900px">
<x-buyer.breadcrumbs :items="[['label'=>'My Orders','url'=>route('buyer.orders')],['label'=>'Rate Product','url'=>null]]" />
<form class="b-card b-review-form" data-buyer-review-form>
<div style="display:flex;gap:14px;align-items:center;margin-bottom:20px"><div class="b-order-image"><img src="{{ asset('images/buyer/products/backpack.svg') }}" alt=""></div><div><strong>Classic Everyday Backpack</strong><div class="b-muted" style="font-size:12px">Variation purchased: Brown</div></div></div>
<div class="b-field"><label>Your Rating</label><div class="b-stars">@for($i=1;$i<=5;$i++)<button class="b-star" type="button" data-review-star>★</button>@endfor</div><input id="reviewRating" name="rating" type="hidden" value="0"></div>
<div class="b-field" style="margin-top:18px"><label for="reviewComment">Review</label><textarea class="b-textarea" id="reviewComment" placeholder="Share your experience..."></textarea></div>
<div class="b-field" style="margin-top:18px"><label for="reviewPhotos">Optional Photos</label><input class="b-input" id="reviewPhotos" type="file" multiple accept="image/*"></div>
<div style="display:flex;gap:9px;flex-wrap:wrap;margin-top:20px"><button class="b-btn b-btn-primary" type="submit">Submit Review</button><a class="b-btn b-btn-secondary" href="{{ route('buyer.orders.show',$id) }}">Cancel</a></div>
</form>
</div></div></x-buyer.layout>
