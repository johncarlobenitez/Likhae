<footer class="sl-footer">
    <p>© {{ date('Y') }} LIKHAE Marketplace · Seller Center</p>
    <nav aria-label="Seller footer links">
        <a href="{{ route('seller.messages') }}">Seller Support</a>
        <a href="{{ route('seller.store', ['tab' => 'settings']) }}">Store Settings</a>
        <a href="{{ route('seller.account', ['tab' => 'security']) }}">Security</a>
    </nav>
</footer>
