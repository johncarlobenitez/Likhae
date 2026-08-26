<footer class="lk-footer">
    <div class="lk-container">
        <div class="lk-footer__grid">
            <div>
                <a class="lk-logo lk-logo--light" href="/">LIKHAE</a>
                <p>An editorial marketplace celebrating Filipino makers, regional craft traditions, and objects made for slow, considered living.</p>
                <p style="margin-top:16px;font-size:0.8rem;color:#8f9ba5">Based in the Philippines · Supporting independent makers nationwide.</p>
            </div>
            <div>
                <h4>Discover</h4>
                <a href="{{ url('/products') }}">All Products</a>
                <a href="{{ url('/products?category=Wear') }}">Wear</a>
                <a href="{{ url('/products?category=Live') }}">Live</a>
                <a href="{{ url('/products?category=Taste') }}">Taste</a>
                <a href="{{ url('/products?category=Glow') }}">Glow</a>
                <a href="{{ url('/products?category=Move') }}">Move</a>
            </div>
            <div>
                <h4>Regions</h4>
                <a href="{{ url('/products?location=Cebu') }}">Cebu Studio Edit</a>
                <a href="{{ url('/products?location=Benguet') }}">Benguet Highland Craft</a>
                <a href="{{ url('/products?location=Marikina') }}">Marikina Leathercraft</a>
                <a href="{{ url('/products?location=Davao') }}">Davao Botanicals</a>
                <a href="{{ url('/products?location=Manila') }}">Manila Independent Design</a>
            </div>
            <div>
                <h4>Trust & Care</h4>
                <a href="{{ route('register') }}">Join as a Buyer</a>
                <a href="{{ route('login') }}">Sign In</a>
                <p>Authentic Local Guarantee</p>
                <p>Protected Transactions</p>
                <p>Verified Maker Network</p>
            </div>
        </div>
        <div class="lk-footer__bottom">
            <span>© {{ date('Y') }} LIKHAE Marketplace. All rights reserved.</span>
            <span>Crafted with intention in the Philippines.</span>
        </div>
    </div>
</footer>

