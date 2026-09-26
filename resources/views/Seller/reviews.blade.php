@extends('layouts.seller')
@section('title', 'Reviews')
@section('active', 'reviews')
@section('content')
<div class="sl-page"><section class="sl-card"><header class="sl-card-head"><div><span class="sl-eyebrow">Feedback</span><h2>Reviews</h2></div></header>@foreach($reviews as $review)<article class="border-t py-4"><strong>{{ $review->rating }}/5</strong><p>{{ $review->comment }}</p><form method="POST" action="{{ route('seller.reviews.reply', $review) }}" class="mt-2 flex gap-2">@csrf<textarea name="seller_reply" class="flex-1 rounded-xl border px-3 py-2">{{ $review->seller_reply }}</textarea><button class="rounded-xl bg-blue-600 px-4 py-2 font-bold text-white">Reply</button></form></article>@endforeach{{ $reviews->links() }}</section></div>
@endsection
