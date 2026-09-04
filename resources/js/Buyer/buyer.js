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

    const showToast = (message) => {
        if (!toast || !message) return;
        toast.textContent = message;
        toast.classList.add('is-visible');
        window.clearTimeout(showToast.timer);
        showToast.timer = window.setTimeout(() => toast.classList.remove('is-visible'), 2800);
    };
    window.lkBuyerToast = showToast;

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
    }));

    all('[data-add-cart]').forEach((button) => button.addEventListener('click', () => {
        const source = button.dataset.quantitySource ? one(button.dataset.quantitySource) : null;
        const quantity = source?.value || 1;
        showToast(`${quantity} item${Number(quantity) === 1 ? '' : 's'} added to your cart.`);
    }));

    all('[data-wishlist]').forEach((button) => button.addEventListener('click', () => {
        const active = button.getAttribute('aria-pressed') !== 'true';
        button.setAttribute('aria-pressed', String(active));
        button.classList.toggle('is-active', active);
        showToast(active ? 'Product saved to your wishlist.' : 'Product removed from your wishlist.');
    }));

    all('[data-demo-action]').forEach((button) => button.addEventListener('click', () => showToast(button.dataset.demoAction)));
    all('[data-demo-form]').forEach((form) => form.addEventListener('submit', (event) => {
        event.preventDefault();
        showToast(form.dataset.successMessage || 'Saved for this frontend preview.');
    }));

    all('[data-account-form]').forEach((form) => form.addEventListener('submit', (event) => {
        event.preventDefault();
        showToast('Account changes saved for this frontend preview.');
    }));
    all('[data-account-page] button[type="button"]:not([data-demo-action])').forEach((button) => button.addEventListener('click', () => {
        showToast('Account action recorded for this frontend preview.');
    }));
});
