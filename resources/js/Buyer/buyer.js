import { initPostalAddressForm } from '../address/postal-code.js';
import './hero-carousel.js';

document.addEventListener('DOMContentLoaded', () => {
    const one = (selector, root = document) => root.querySelector(selector);
    const all = (selector, root = document) => [...root.querySelectorAll(selector)];
    const body = document.body;
    const sidebar = one('[data-lk-sidebar]');
    const mobileButton = one('[data-lk-mobile-menu]');
    const collapseButton = one('[data-lk-sidebar-toggle]');
    const overlay = one('[data-lk-overlay]');
    const toast = one('#lkBuyerToast');
    const desktop = window.matchMedia('(min-width: 1024px)');
    const storageKey = 'likhae-buyer-sidebar-collapsed';
    const accountMenu = one('[data-account-menu]');
    const accountMenuToggle = one('[data-account-menu-toggle]', accountMenu || document);
    const accountMenuPanel = one('[data-account-menu-panel]', accountMenu || document);

    all('[data-postal-address]').forEach(initPostalAddressForm);

    const showToast = (message) => {
        if (!toast || !message) return;
        toast.textContent = message;
        toast.classList.add('is-visible');
        window.clearTimeout(showToast.timer);
        showToast.timer = window.setTimeout(() => toast.classList.remove('is-visible'), 2800);
    };
    window.lkBuyerToast = showToast;

    const setAccountMenu = (open) => {
        if (!accountMenuToggle || !accountMenuPanel) return;
        accountMenuToggle.setAttribute('aria-expanded', String(open));
        accountMenuPanel.hidden = !open;
    };

    accountMenuToggle?.addEventListener('click', () => setAccountMenu(accountMenuPanel?.hidden));
    document.addEventListener('click', (event) => {
        if (accountMenu && !accountMenu.contains(event.target)) setAccountMenu(false);
    });

    const storedCollapsed = () => {
        try { return localStorage.getItem(storageKey) === 'true'; } catch (_) { return false; }
    };

    const setCollapsed = (collapsed, persist = true) => {
        if (!sidebar || !desktop.matches) return;
        sidebar.classList.toggle('lk-sidebar-collapsed', collapsed);
        body.classList.toggle('lk-sidebar-is-collapsed', collapsed);
        collapseButton?.setAttribute('aria-expanded', String(!collapsed));
        collapseButton?.setAttribute('aria-label', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
        if (persist) {
            try { localStorage.setItem(storageKey, String(collapsed)); } catch (_) {}
        }
    };

    const setMobileOpen = (open) => {
        if (!sidebar) return;
        sidebar.classList.toggle('lk-sidebar-open', open);
        overlay?.classList.toggle('lk-overlay-open', open);
        body.classList.toggle('lk-mobile-sidebar-open', open && !desktop.matches);
        mobileButton?.setAttribute('aria-expanded', String(open));
    };

    if (desktop.matches) setCollapsed(storedCollapsed(), false);
    mobileButton?.addEventListener('click', () => setMobileOpen(!sidebar?.classList.contains('lk-sidebar-open')));
    overlay?.addEventListener('click', () => setMobileOpen(false));
    collapseButton?.addEventListener('click', () => {
        if (!desktop.matches) {
            setMobileOpen(false);
            return;
        }
        setCollapsed(!sidebar?.classList.contains('lk-sidebar-collapsed'));
    });

    const submenuFor = (toggle) => toggle.nextElementSibling?.matches('[data-nav-submenu]') ? toggle.nextElementSibling : null;
    const setSubmenu = (toggle, open) => {
        const submenu = submenuFor(toggle);
        if (!submenu) return;
        toggle.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', String(open));
        if (open) {
            submenu.hidden = false;
            requestAnimationFrame(() => submenu.classList.add('expanded'));
        } else {
            submenu.classList.remove('expanded');
            window.setTimeout(() => { if (!submenu.classList.contains('expanded')) submenu.hidden = true; }, 190);
        }
    };

    all('[data-nav-toggle]').forEach((toggle) => {
        const submenu = submenuFor(toggle);
        if (!submenu) return;
        if (!submenu.hidden) submenu.classList.add('expanded');
        toggle.addEventListener('click', () => {
            if (desktop.matches && sidebar?.classList.contains('lk-sidebar-collapsed')) setCollapsed(false);
            const opening = toggle.getAttribute('aria-expanded') !== 'true';
            if (opening) all('[data-nav-toggle]').filter((item) => item !== toggle).forEach((item) => setSubmenu(item, false));
            setSubmenu(toggle, opening);
        });
    });

    all('.lk-sidebar a').forEach((link) => link.addEventListener('click', () => {
        if (!desktop.matches) setMobileOpen(false);
    }));

    desktop.addEventListener?.('change', (event) => {
        setMobileOpen(false);
        body.classList.remove('lk-sidebar-is-collapsed');
        sidebar?.classList.remove('lk-sidebar-collapsed');
        if (event.matches) setCollapsed(storedCollapsed(), false);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') setMobileOpen(false);
        if (event.key === 'Escape') setAccountMenu(false);
    });

    const authDialog = one('[data-auth-dialog]');
    all('[data-auth-required]').forEach((button) => button.addEventListener('click', (event) => {
        event.preventDefault();
        const message = button.dataset.authMessage || 'Sign in or create a Buyer account to continue.';
        const copy = one('[data-auth-message]', authDialog || document);
        if (copy) copy.textContent = message;
        if (authDialog?.showModal) authDialog.showModal();
        else window.location.href = button.dataset.loginUrl || '/login';
    }));

    all('[data-detail-thumbnail], [data-gallery-thumb]').forEach((thumbnail) => thumbnail.addEventListener('click', () => {
        const gallery = thumbnail.closest('[data-product-gallery]') || document;
        const mainImage = one('[data-detail-main-image], [data-gallery-main]', gallery);
        if (!mainImage || !thumbnail.dataset.image) return;
        mainImage.src = thumbnail.dataset.image;
        all('[data-detail-thumbnail], [data-gallery-thumb]', gallery).forEach((item) => {
            item.classList.toggle('is-active', item === thumbnail);
            item.classList.toggle('border-red-800', item === thumbnail);
            item.classList.toggle('border-transparent', item !== thumbnail);
            item.setAttribute('aria-pressed', String(item === thumbnail));
        });
    }));

    const syncVariantGallery = (button) => {
        const gallery = button?.closest('[data-variant-stock-product]')?.closest('section')?.querySelector('[data-product-gallery]')
            || document.querySelector('[data-product-gallery]');
        if (!gallery) return;

        let images = [];
        try { images = JSON.parse(button?.dataset.variationImages || '[]').filter(Boolean); } catch (_) { images = []; }
        if (!images.length) {
            try { images = JSON.parse(gallery.dataset.galleryBaseImages || '[]').filter(Boolean); } catch (_) { images = []; }
        }
        if (!images.length) return;

        const mainImage = one('[data-detail-main-image], [data-gallery-main]', gallery);
        if (mainImage) mainImage.src = images[0];

        let thumbs = one('[data-gallery-thumbs]', gallery) || one('.lk-detail-thumbs', gallery);
        if (!thumbs) {
            thumbs = document.createElement('div');
            thumbs.className = 'lk-detail-thumbs';
            thumbs.dataset.galleryThumbs = 'true';
            gallery.appendChild(thumbs);
        }
        thumbs.innerHTML = '';
        thumbs.hidden = images.length < 2;
        images.forEach((url, index) => {
            const thumb = document.createElement('button');
            thumb.type = 'button';
            thumb.className = `lk-detail-thumb${index === 0 ? ' is-active' : ''}`;
            thumb.dataset.galleryThumb = 'true';
            thumb.dataset.image = url;
            thumb.setAttribute('aria-label', `View variation image ${index + 1}`);
            const image = document.createElement('img');
            image.src = url;
            image.alt = `Variation image ${index + 1}`;
            thumb.appendChild(image);
            thumb.addEventListener('click', () => {
                if (mainImage) mainImage.src = url;
                all('[data-gallery-thumb]', gallery).forEach((item) => item.classList.toggle('is-active', item === thumb));
            });
            thumbs.appendChild(thumb);
        });
    };

    all('[data-quantity-control]').forEach((control) => {
        const input = one('input', control);
        const clamp = (value) => Math.min(Number(input?.max || 99), Math.max(Number(input?.min || 1), value));
        one('[data-quantity-minus]', control)?.addEventListener('click', () => { if (input) input.value = clamp(Number(input.value || 1) - 1); });
        one('[data-quantity-plus]', control)?.addEventListener('click', () => { if (input) input.value = clamp(Number(input.value || 1) + 1); });
        input?.addEventListener('change', () => { input.value = clamp(Number(input.value || 1)); });
    });

    all('[data-variation-option]').forEach((button) => button.addEventListener('click', () => {
        const group = button.closest('[data-variation-group]');
        all('[data-variation-option]', group || document).forEach((option) => {
            const selected = option === button;
            option.setAttribute('aria-pressed', String(selected));
            option.classList.toggle('border-red-800', selected);
            option.classList.toggle('bg-red-50', selected);
            option.classList.toggle('text-red-900', selected);
            option.classList.toggle('border-stone-300', !selected);
            option.classList.toggle('text-stone-700', !selected);
        });
        syncVariantGallery(button);
    }));

    const selectedVariation = (scope) => {
        const selected = one('[data-variation-option][aria-pressed="true"]', scope);
        if (!selected) return null;

        return {
            id: selected.dataset.variationId || '',
            value: selected.dataset.variationValue || '',
            stock: Number(selected.dataset.variationStock || 0),
            price: Number(selected.dataset.variationPrice || 0),
            originalPrice: Number(selected.dataset.variationOriginalPrice || selected.dataset.variationPrice || 0),
            discountType: selected.dataset.variationDiscountType || 'none',
            discountValue: Number(selected.dataset.variationDiscountValue ?? selected.dataset.variationDiscount ?? 0),
            discountAmount: Number(selected.dataset.variationDiscountAmount || 0),
        };
    };

    all('[data-test-add-cart], [data-test-buy-now]').forEach((button) => button.addEventListener('click', (event) => {
        const scope = event.currentTarget.closest('.lk-detail-info-card') || document;
        const variation = selectedVariation(scope);
        const cartUrl = event.currentTarget.dataset.cartUrl;

        if (!variation?.id || !cartUrl) {
            showToast('Select an available product variation first.');
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = cartUrl;
        form.style.display = 'none';

        const fields = {
            _token: one('meta[name="csrf-token"]')?.content || '',
            product_variant_id: variation.id,
            quantity: one('[data-quantity-input]', scope)?.value || 1,
        };

        if (event.currentTarget.dataset.checkout === '1') fields.checkout = '1';

        Object.entries(fields).forEach(([name, value]) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        });

        event.currentTarget.disabled = true;
        document.body.appendChild(form);
        form.submit();
    }));

    all('[data-variant-stock-product]').forEach((product) => {
        let stocks = {};
        try { stocks = JSON.parse(product.dataset.variantStocks || '{}'); } catch (_) { return; }
        const sync = () => {
            const variation = selectedVariation(product);
            const stock = variation?.id ? Number(stocks[variation.id] ?? variation.stock ?? 0) : 0;
            const availability = one('[data-variant-availability]', product);
            const quantity = one('[data-quantity-input]', product);
            const price = one('.lk-detail-price', product);
            if (availability) availability.textContent = stock ? `${stock} available` : 'Out of stock';
            if (price && variation?.id) price.textContent = `₱${variation.price.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            const oldPrice = one('[data-variation-old-price]', product);
            const discountBadge = one('[data-variation-discount-badge-wrapper]', product);
            const discountBadgeText = one('[data-variation-discount-badge]', product);
            const discountType = String(variation?.discountType || 'none').toLowerCase();
            const discountAmount = variation?.discountAmount || (discountType === 'fixed'
                ? Math.min(variation?.originalPrice || 0, variation?.discountValue || 0)
                : (variation?.originalPrice || 0) * Math.min(100, variation?.discountValue || 0) / 100);
            if (oldPrice && variation?.id) {
                oldPrice.textContent = `₱${variation.originalPrice.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                oldPrice.hidden = !(discountAmount > 0 && variation.originalPrice > variation.price);
            }
            if (discountBadge) {
                discountBadge.hidden = !(discountAmount > 0);
                if (discountBadgeText) {
                    discountBadgeText.textContent = discountType === 'fixed'
                        ? `₱${(variation?.discountValue || 0).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})} OFF`
                        : `${(variation?.discountValue || 0).toLocaleString('en-PH', {maximumFractionDigits: 2})}% OFF`;
                }
            }
            if (quantity) { quantity.max = Math.max(1, stock); quantity.value = Math.min(Number(quantity.value || 1), Math.max(1, stock)); }
            all('[data-product-purchase]', product).forEach((control) => { control.disabled = stock < 1; control.setAttribute('aria-disabled', String(stock < 1)); });
        };
        all('[data-variation-option]', product).forEach((button) => button.addEventListener('click', sync));
        sync();
        syncVariantGallery(one('[data-variation-option][aria-pressed="true"]', product));
    });

    all('[data-add-cart]').forEach((button) => button.addEventListener('click', () => {
        const source = button.dataset.quantitySource ? one(button.dataset.quantitySource) : null;
        const quantity = source?.value || 1;
        showToast(`${quantity} item${Number(quantity) === 1 ? '' : 's'} added to your cart.`);
    }));

    all('[data-wishlist]').forEach((button) => {
        if (document.querySelector('[data-wishlist-grid]')) {
            button.setAttribute('aria-pressed', 'true');
            button.classList.add('is-active');
            button.setAttribute('aria-label', 'Remove product from wishlist');
        }
        button.addEventListener('click', () => {
        const productId = button.dataset.productId;
        if (!productId) return;

        const active = button.getAttribute('aria-pressed') !== 'true';
        button.setAttribute('aria-pressed', String(active));
        button.classList.toggle('is-active', active);
        showToast(active ? 'Product saved to your wishlist.' : 'Product removed from your wishlist.');

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/buyer/wishlist/${encodeURIComponent(productId)}`;
        form.style.display = 'none';

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (token) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = '_token';
            input.value = token;
            form.appendChild(input);
        }

        document.body.appendChild(form);
        form.submit();
        });
    });

    const wishlistForm = one('[data-wishlist-selection-form]');
    all('[data-remove-wishlist-item]').forEach((button) => button.addEventListener('click', () => {
        const productId = button.dataset.removeWishlistItem;
        if (!productId) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/buyer/wishlist/${encodeURIComponent(productId)}`;
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (token) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = '_token';
            input.value = token;
            form.appendChild(input);
        }
        document.body.appendChild(form);
        form.submit();
    }));

    if (wishlistForm) {
        const checkboxes = all('[data-wishlist-select]', wishlistForm);
        const removeButton = one('[data-remove-selected]', wishlistForm);
        const count = one('[data-wishlist-selection-count]', wishlistForm);
        const selectAll = one('[data-select-all-wishlist]', wishlistForm);
        const syncSelection = () => {
            const selected = checkboxes.filter((checkbox) => checkbox.checked).length;
            if (removeButton) removeButton.disabled = selected === 0;
            if (count) count.textContent = `${selected} selected`;
            if (selectAll) selectAll.textContent = selected === checkboxes.length ? 'Deselect All' : 'Select All';
        };
        checkboxes.forEach((checkbox) => checkbox.addEventListener('change', syncSelection));
        selectAll?.addEventListener('click', () => {
            const shouldSelect = checkboxes.some((checkbox) => !checkbox.checked);
            checkboxes.forEach((checkbox) => { checkbox.checked = shouldSelect; });
            syncSelection();
        });
        wishlistForm.addEventListener('submit', (event) => {
            if (!checkboxes.some((checkbox) => checkbox.checked)) event.preventDefault();
        });
        syncSelection();
    }

});
