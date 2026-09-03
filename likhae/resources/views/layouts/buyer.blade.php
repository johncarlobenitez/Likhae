@php
    $pageTitle  = trim($__env->yieldContent('title'))  ?: 'Buyer';
    $activePage = trim($__env->yieldContent('active')) ?: 'home';
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }} · LIKHAE</title>
    @vite(['resources/css/Buyer/buyer.css', 'resources/js/buyer/buyer.js'])
</head>
<body class="lk-buyer-body">

    @include('components.buyer.sidebar', ['active' => $activePage])

    <div class="lk-shell">
        @include('components.buyer.header', ['title' => $pageTitle])
        <main class="lk-main">
            @yield('content')
        </main>
        @include('components.buyer.footer')
    </div>

    <div class="lk-overlay" data-lk-overlay></div>
    <div id="lkBuyerToast" class="lk-toast" aria-live="polite"></div>

    {{-- Product Quick-View Modal --}}
    <div id="lkModalOverlay" style="display:none;position:fixed;inset:0;z-index:200;background:rgba(20,15,10,.55);backdrop-filter:blur(4px);overflow-y:auto;padding:24px 16px;">
        <div id="lkProductModal" style="position:relative;width:min(960px,100%);margin:0 auto;background:#fff;border-radius:22px;box-shadow:0 32px 80px rgba(32,20,10,.18);overflow:hidden;opacity:0;transform:translateY(24px) scale(.97);transition:opacity 260ms ease,transform 260ms ease;" role="dialog" aria-modal="true" aria-label="Product details">
            <button id="lkModalClose" type="button" style="position:absolute;top:14px;right:14px;z-index:10;display:grid;width:36px;height:36px;place-items:center;padding:0;background:rgba(255,255,255,.92);border:1px solid var(--lk-border);border-radius:50%;color:var(--lk-muted);cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.08);" aria-label="Close">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
            <div id="lkModalBody"></div>
        </div>
    </div>

<script>
var lkProducts = @json(collect($buyerProducts ?? [])->values());
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var sidebar   = document.querySelector('.lk-sidebar');
  var overlay   = document.querySelector('.lk-overlay');
  var mobileBtn = document.querySelector('[data-lk-mobile-menu]');
  var collapseBtn = document.querySelector('[data-lk-sidebar-toggle]');

  if (mobileBtn) {
    mobileBtn.addEventListener('click', function () {
      var open = sidebar.classList.contains('lk-sidebar-open');
      sidebar.classList.toggle('lk-sidebar-open', !open);
      overlay.classList.toggle('lk-overlay-open', !open);
      document.body.classList.toggle('lk-mobile-sidebar-open', !open);
      document.body.style.overflow = open ? '' : 'hidden';
    });
  }

  if (overlay) {
    overlay.addEventListener('click', function () {
      sidebar.classList.remove('lk-sidebar-open');
      overlay.classList.remove('lk-overlay-open');
      document.body.classList.remove('lk-mobile-sidebar-open');
      document.body.style.overflow = '';
    });
  }

  if (collapseBtn) {
    collapseBtn.addEventListener('click', function () {
      var collapsed = sidebar.classList.toggle('lk-sidebar-collapsed');
      document.body.classList.toggle('lk-sidebar-collapsed', collapsed);
      if (collapsed) {
        document.querySelectorAll('.lk-nav-submenu.expanded').forEach(function (sm) {
          sm.classList.remove('expanded');
          var t = sm.previousElementSibling;
          if (t) { t.classList.remove('is-open'); t.setAttribute('aria-expanded','false'); }
        });
      }
    });
  }

  document.querySelectorAll('[data-nav-toggle]').forEach(function (toggle) {
    var submenu = toggle.nextElementSibling;
    while (submenu && !submenu.hasAttribute('data-nav-submenu')) {
      submenu = submenu.nextElementSibling;
    }
    if (!submenu) return;

    toggle.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      if (sidebar && sidebar.classList.contains('lk-sidebar-collapsed')) return;
      var isOpen = submenu.classList.contains('expanded');
      document.querySelectorAll('.lk-nav-submenu.expanded').forEach(function (other) {
        if (other !== submenu) {
          other.classList.remove('expanded');
          var t = other.previousElementSibling;
          if (t) { t.classList.remove('is-open'); t.setAttribute('aria-expanded','false'); }
        }
      });
      if (isOpen) {
        submenu.classList.remove('expanded');
        toggle.classList.remove('is-open');
        toggle.setAttribute('aria-expanded','false');
      } else {
        submenu.classList.add('expanded');
        toggle.classList.add('is-open');
        toggle.setAttribute('aria-expanded','true');
      }
    });
  });

  document.querySelectorAll('.lk-nav-submenu').forEach(function (submenu) {
    if (submenu.querySelector('.is-active')) {
      var t = submenu.previousElementSibling;
      if (t) { submenu.classList.add('expanded'); t.classList.add('is-open'); t.setAttribute('aria-expanded','true'); }
    }
  });

  /* ── Add to Cart ── */
  function showToast(msg) {
    var t = document.getElementById('lkBuyerToast');
    if (!t) return;
    t.textContent = msg;
    t.classList.add('lk-toast-show');
    clearTimeout(t._tid);
    t._tid = setTimeout(function(){ t.classList.remove('lk-toast-show'); }, 2800);
  }

  document.addEventListener('click', function(e) {
    var btn = e.target.closest('[data-add-cart]');
    if (!btn || btn.disabled) return;
    var id = btn.getAttribute('data-product-id');
    var p = (lkProducts || []).find(function(x){ return String(x.id||x.slug) === String(id); });
    var name = p ? p.name : 'Item';
    showToast('\u2713 ' + name + ' added to cart!');
    btn.classList.add('lk-add-cart-added');
    setTimeout(function(){ btn.classList.remove('lk-add-cart-added'); }, 1400);
  });
});
</script>
<style>
.lk-toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(20px);z-index:300;padding:11px 20px;background:var(--lk-ink);color:#fff;border-radius:12px;font-size:11px;font-weight:600;opacity:0;pointer-events:none;transition:opacity 220ms ease,transform 220ms ease;white-space:nowrap;}
.lk-toast.lk-toast-show{opacity:1;transform:translateX(-50%) translateY(0);}
.lk-add-cart-added{background:#287a51!important;border-color:#287a51!important;color:#fff!important;}
</style>
@stack('scripts')

</body>
</html>
