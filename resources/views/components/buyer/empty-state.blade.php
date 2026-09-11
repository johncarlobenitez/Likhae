@props(['title' => 'Nothing here yet', 'message' => 'Once there is activity, it will appear here.', 'action' => null, 'href' => null])
<div class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-12 text-center shadow-sm">
    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-2xl font-bold text-red-900">⌁</div>
    <h3 class="mt-4 text-base font-bold text-stone-900">{{ $title }}</h3>
    <p class="mx-auto mt-1 max-w-md text-xs leading-5 text-stone-500">{{ $message }}</p>
    @if($action && $href)
        <a class="lk-btn lk-btn-red mt-5" href="{{ $href }}">{{ $action }}</a>
    @endif
</div>
