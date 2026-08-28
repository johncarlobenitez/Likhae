@props(['id'=>'buyerModal','title'=>'Modal'])
<dialog id="{{ $id }}" class="b-card" style="width:min(520px,calc(100% - 30px));padding:0;border:1px solid var(--b)">
<div style="display:flex;justify-content:space-between;padding:18px;border-bottom:1px solid var(--b)"><strong>{{ $title }}</strong><button type="button" onclick="this.closest('dialog').close()">✕</button></div>
<div style="padding:18px">{{ $slot }}</div>
</dialog>
