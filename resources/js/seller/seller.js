document.addEventListener('DOMContentLoaded', () => {
    const select = (selector, root = document) => root.querySelector(selector);
    const selectAll = (selector, root = document) => Array.from(root.querySelectorAll(selector));
    const sidebar = select('[data-sl-sidebar]');
    const overlay = select('[data-sl-overlay]');
    const mobileButton = select('[data-sl-mobile-menu]');
    const collapseButton = select('[data-sl-sidebar-toggle]');
    const desktop = window.matchMedia('(min-width: 1024px)');
    const storageKey = 'likhae-seller-sidebar-collapsed';
    let toastTimer;

    const readStorage = (key) => {
        try { return window.localStorage.getItem(key); } catch (error) { return null; }
    };

    const writeStorage = (key, value) => {
        try { window.localStorage.setItem(key, value); } catch (error) { /* State remains session-only. */ }
    };

    const showToast = (message) => {
        const toast = select('[data-sl-toast]');
        if (!toast || !message) return;
        window.clearTimeout(toastTimer);
        toast.textContent = message;
        toast.classList.add('is-visible');
        toastTimer = window.setTimeout(() => toast.classList.remove('is-visible'), 2600);
    };

    const setMobileSidebar = (open) => {
        if (!sidebar) return;
        sidebar.classList.toggle('is-open', open);
        overlay?.classList.toggle('is-open', open);
        document.body.classList.toggle('sl-mobile-open', open && !desktop.matches);
        mobileButton?.setAttribute('aria-expanded', String(open));
    };

    const setCollapsed = (collapsed, persist = true) => {
        if (!desktop.matches) collapsed = false;
        document.body.classList.toggle('sl-sidebar-collapsed', collapsed);
        collapseButton?.setAttribute('aria-expanded', String(!collapsed));
        collapseButton?.setAttribute('aria-label', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
        if (persist) writeStorage(storageKey, String(collapsed));
    };

    if (desktop.matches && readStorage(storageKey) === 'true') setCollapsed(true, false);
    mobileButton?.addEventListener('click', () => setMobileSidebar(!sidebar?.classList.contains('is-open')));
    overlay?.addEventListener('click', () => setMobileSidebar(false));
    collapseButton?.addEventListener('click', () => setCollapsed(!document.body.classList.contains('sl-sidebar-collapsed')));

    desktop.addEventListener('change', (event) => {
        setMobileSidebar(false);
        setCollapsed(event.matches && readStorage(storageKey) === 'true', false);
    });

    selectAll('.sl-sidebar a').forEach((link) => link.addEventListener('click', () => {
        if (!desktop.matches) setMobileSidebar(false);
    }));

    selectAll('[data-nav-toggle]').forEach((button) => {
        const submenu = button.nextElementSibling;
        if (!submenu?.matches('[data-nav-submenu]')) return;

        button.addEventListener('click', () => {
            const open = !button.classList.contains('is-open');
            selectAll('[data-nav-toggle]').forEach((other) => {
                if (other === button) return;
                const otherMenu = other.nextElementSibling;
                other.classList.remove('is-open');
                other.setAttribute('aria-expanded', 'false');
                if (otherMenu?.matches('[data-nav-submenu]')) {
                    otherMenu.classList.remove('is-open');
                    window.setTimeout(() => { if (!otherMenu.classList.contains('is-open')) otherMenu.hidden = true; }, 200);
                }
            });

            button.classList.toggle('is-open', open);
            button.setAttribute('aria-expanded', String(open));
            if (open) {
                submenu.hidden = false;
                window.requestAnimationFrame(() => submenu.classList.add('is-open'));
            } else {
                submenu.classList.remove('is-open');
                window.setTimeout(() => { if (!submenu.classList.contains('is-open')) submenu.hidden = true; }, 200);
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
            const search = select('.sl-header-search input');
            if (search) { event.preventDefault(); search.focus(); }
        }
        if (event.key === 'Escape') {
            setMobileSidebar(false);
            closeModal(select('.sl-modal:not([hidden])'));
        }
    });

    selectAll('[data-demo-action]').forEach((button) => {
        button.addEventListener('click', (event) => {
            if (button.tagName === 'A' && button.getAttribute('href') !== '#') return;
            event.preventDefault();
            showToast(button.dataset.demoAction);
        });
    });

    const openModal = (modal) => {
        if (!modal) return;
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
        select('input, select, textarea, button', modal)?.focus();
    };

    function closeModal(modal) {
        if (!modal) return;
        modal.hidden = true;
        document.body.style.overflow = '';
    }

    selectAll('[data-modal-open]').forEach((button) => button.addEventListener('click', () => {
        openModal(select(`[data-modal="${button.dataset.modalOpen}"]`));
    }));
    selectAll('[data-modal-close]').forEach((button) => button.addEventListener('click', () => closeModal(button.closest('[data-modal]'))));
    selectAll('[data-modal]').forEach((modal) => modal.addEventListener('click', (event) => {
        if (event.target === modal) closeModal(modal);
    }));

    selectAll('[data-demo-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            if (!form.reportValidity()) return;
            showToast(form.dataset.success || 'Changes saved successfully.');
            closeModal(form.closest('[data-modal]'));
        });
    });

    const characterField = select('[data-character-count]');
    if (characterField) {
        const textarea = characterField.closest('.sl-field')?.querySelector('textarea');
        const updateCount = () => { characterField.textContent = String(textarea?.value.length || 0); };
        textarea?.addEventListener('input', updateCount);
        updateCount();
    }

    select('[data-image-input]')?.addEventListener('change', (event) => {
        const preview = select('[data-image-preview]');
        if (!preview) return;
        preview.innerHTML = '';
        Array.from(event.target.files || []).slice(0, 5).forEach((file) => {
            const image = document.createElement('img');
            image.src = URL.createObjectURL(file);
            image.alt = file.name;
            image.addEventListener('load', () => URL.revokeObjectURL(image.src), { once: true });
            preview.appendChild(image);
        });
    });

    select('[data-add-variation]')?.addEventListener('click', () => {
        const list = select('[data-variation-list]');
        if (!list) return;
        const row = document.createElement('div');
        row.className = 'sl-variation-row';
        row.innerHTML = '<input placeholder="e.g. Color" aria-label="Variation name"><input placeholder="e.g. Navy Blue" aria-label="Variation option"><input placeholder="SKU" aria-label="Variation SKU"><input type="number" min="0" value="0" aria-label="Variation stock"><button type="button" class="sl-icon-btn" data-remove-row aria-label="Remove variation">×</button>';
        list.appendChild(row);
        select('input', row)?.focus();
    });

    document.addEventListener('click', (event) => {
        const removeButton = event.target.closest('[data-remove-row]');
        if (removeButton) removeButton.closest('.sl-variation-row')?.remove();
    });

    const filterProductRows = () => {
        const query = (select('[data-product-search]')?.value || '').trim().toLowerCase();
        const status = select('[data-product-status-filter]')?.value || 'all';
        const rows = selectAll('[data-product-row]');
        let visible = 0;
        rows.forEach((row) => {
            const matchesSearch = !query || (row.dataset.productName || '').includes(query);
            const matchesStatus = status === 'all' || row.dataset.productStatus === status;
            const show = matchesSearch && matchesStatus;
            row.hidden = !show;
            if (show) visible += 1;
        });
        const empty = select('[data-product-empty]');
        if (empty) empty.hidden = visible > 0;
    };

    select('[data-product-search]')?.addEventListener('input', filterProductRows);
    select('[data-product-status-filter]')?.addEventListener('change', filterProductRows);

    selectAll('[data-stock-update]').forEach((button) => button.addEventListener('click', () => {
        const product = button.dataset.product || 'this product';
        const value = window.prompt(`Enter the new available stock for ${product}:`);
        if (value === null) return;
        const stock = Number.parseInt(value, 10);
        if (!Number.isFinite(stock) || stock < 0) {
            showToast('Enter a valid stock quantity.');
            return;
        }
        showToast(`${product} stock updated to ${stock}.`);
    }));

    selectAll('[data-order-action]').forEach((button) => button.addEventListener('click', () => {
        const card = button.closest('[data-order-card]');
        const label = select('[data-order-status]', card);
        if (button.dataset.orderAction === 'accept') {
            if (label) label.textContent = 'To Prepare';
            button.textContent = 'Order Accepted';
            button.disabled = true;
            showToast('Order accepted and moved to To Prepare.');
        } else {
            if (label) label.textContent = 'Ready Pickup';
            button.textContent = 'Package Prepared';
            button.disabled = true;
            showToast('Package marked ready for courier assignment.');
        }
    }));

    const orderSearch = select('[data-order-search]');
    orderSearch?.addEventListener('input', () => {
        const query = orderSearch.value.trim().toLowerCase();
        const cards = selectAll('[data-order-card]');
        let visible = 0;
        cards.forEach((card) => {
            const show = !query || (card.dataset.search || '').includes(query);
            card.hidden = !show;
            if (show) visible += 1;
        });
        const empty = select('[data-order-empty]');
        if (empty) empty.hidden = visible > 0;
    });
    if (orderSearch?.value.trim()) orderSearch.dispatchEvent(new Event('input'));

    const filterConversations = () => {
        const query = (select('[data-conversation-search]')?.value || '').trim().toLowerCase();
        const activeFilter = select('[data-conversation-filter].is-active')?.dataset.conversationFilter || 'all';
        selectAll('[data-conversation]').forEach((item) => {
            const matchesQuery = !query || (item.dataset.search || '').includes(query);
            const matchesFilter = activeFilter === 'all' || (activeFilter === 'unread' && item.dataset.unread === 'true') || (activeFilter === 'orders' && item.dataset.order === 'true');
            item.hidden = !(matchesQuery && matchesFilter);
        });
    };

    select('[data-conversation-search]')?.addEventListener('input', filterConversations);
    selectAll('[data-conversation-filter]').forEach((button) => button.addEventListener('click', () => {
        selectAll('[data-conversation-filter]').forEach((item) => item.classList.toggle('is-active', item === button));
        filterConversations();
    }));

    const conversationContent = {
        angela: ['AC', 'Angela Cruz', 'Order #10001', '27-inch Borderless Monitor'],
        marco: ['MR', 'Marco Reyes', 'Order #10002', 'Mechanical Keyboard 87 Keys'],
        sarah: ['SL', 'Sarah Lim', 'Order #10003', 'Wireless Gaming Mouse'],
        daniel: ['DT', 'Daniel Tan', 'Order #10004', 'Studio Wireless Headphones'],
        patricia: ['PG', 'Patricia Go', 'Product question', 'Adjustable LED Desk Lamp'],
    };

    selectAll('[data-conversation]').forEach((button) => button.addEventListener('click', () => {
        selectAll('[data-conversation]').forEach((item) => item.classList.toggle('is-active', item === button));
        const data = conversationContent[button.dataset.key] || ['BY', button.dataset.name, 'Conversation', 'Product inquiry'];
        const avatar = select('[data-chat-avatar]');
        const name = select('[data-chat-name]');
        const order = select('[data-chat-order]');
        const product = select('[data-chat-product]');
        if (avatar) avatar.textContent = data[0];
        if (name) name.textContent = data[1];
        if (order) order.textContent = data[2];
        if (product) product.textContent = data[3];
        button.dataset.unread = 'false';
        select('.sl-unread', button)?.remove();
    }));

    select('[data-chat-form]')?.addEventListener('submit', (event) => {
        event.preventDefault();
        const input = select('[data-chat-input]', event.currentTarget);
        const list = select('[data-chat-messages]');
        const message = input?.value.trim();
        if (!message || !list) return;
        const bubble = document.createElement('div');
        bubble.className = 'sl-message is-seller';
        bubble.innerHTML = `<p></p><small>Just now · Sending</small>`;
        select('p', bubble).textContent = message;
        list.appendChild(bubble);
        input.value = '';
        list.scrollTop = list.scrollHeight;
        window.setTimeout(() => { const time = select('small', bubble); if (time) time.textContent = 'Just now · Sent'; }, 550);
    });

    const filterNotifications = (type) => {
        let visible = 0;
        selectAll('[data-notification]').forEach((item) => {
            const show = type === 'all' || item.dataset.type === type;
            item.hidden = !show;
            if (show) visible += 1;
        });
        const empty = select('[data-notification-empty]');
        if (empty) empty.hidden = visible > 0;
    };

    selectAll('[data-notification-filter]').forEach((button) => button.addEventListener('click', () => {
        selectAll('[data-notification-filter]').forEach((item) => item.classList.toggle('is-active', item === button));
        filterNotifications(button.dataset.notificationFilter);
    }));

    select('[data-mark-all-read]')?.addEventListener('click', () => {
        selectAll('[data-notification].is-unread').forEach((item) => {
            item.classList.remove('is-unread');
            Array.from(item.children).find((child) => child.tagName === 'I')?.remove();
        });
        showToast('All notifications marked as read.');
    });
});
