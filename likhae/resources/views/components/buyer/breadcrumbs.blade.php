@props(['items'=>[]])
<nav class="b-breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('buyer.home') }}">Home</a>@foreach($items as $item)<span>›</span>@if(!empty($item['url']))<a href="{{ $item['url'] }}">{{ $item['label'] }}</a>@else<span>{{ $item['label'] }}</span>@endif @endforeach</nav>
