<div class="g-toast-wrap" id="guestToastWrap">
    @if(session('status'))
        <div class="g-toast">{{ session('status') }}</div>
    @endif
</div>
