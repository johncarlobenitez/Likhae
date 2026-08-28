@props(['title'=>'Nothing here yet','message'=>'There is no content to display right now.','action'=>null,'href'=>'#'])
<div class="b-card b-empty"><div class="b-empty-icon">🛍️</div><h3>{{ $title }}</h3><p>{{ $message }}</p>@if($action)<a class="b-btn b-btn-primary" href="{{ $href }}">{{ $action }}</a>@endif</div>
