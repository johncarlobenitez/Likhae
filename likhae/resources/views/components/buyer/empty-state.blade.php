@props(['title' => 'Nothing here yet', 'message' => 'Once there is activity, it will appear here.', 'action' => null, 'href' => null])
<div class="lk-empty">
    <div class="lk-empty-icon">⌁</div>
    <h3>{{ $title }}</h3>
    <p>{{ $message }}</p>
    @if($action && $href)
        <a class="lk-btn lk-btn-dark" href="{{ $href }}">{{ $action }}</a>
    @endif
</div>
