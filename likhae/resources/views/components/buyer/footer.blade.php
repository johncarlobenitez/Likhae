<footer style="border-top:1px solid var(--lk-border);background:#fff;padding:18px 28px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
    <p style="margin:0;font-size:11px;color:var(--lk-muted);">
        &copy; {{ now()->year }} <strong style="color:var(--lk-ink);">LIKHAE Marketplace</strong> &mdash; Buyer-first shopping experience.
    </p>
    <nav style="display:flex;gap:20px;" aria-label="Footer navigation">
        <a href="{{ route('buyer.orders') }}"       style="font-size:11px;color:var(--lk-muted);transition:color .15s;" onmouseover="this.style.color='var(--lk-red)'" onmouseout="this.style.color='var(--lk-muted)'">Track Orders</a>
        <a href="{{ route('buyer.messages') }}"     style="font-size:11px;color:var(--lk-muted);transition:color .15s;" onmouseover="this.style.color='var(--lk-red)'" onmouseout="this.style.color='var(--lk-muted)'">Support</a>
        <a href="{{ route('buyer.account') }}"      style="font-size:11px;color:var(--lk-muted);transition:color .15s;" onmouseover="this.style.color='var(--lk-red)'" onmouseout="this.style.color='var(--lk-muted)'">Account</a>
    </nav>
</footer>
