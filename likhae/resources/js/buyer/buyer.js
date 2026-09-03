/**
 * LIKHAE Buyer — buyer.js
 */

document.addEventListener('DOMContentLoaded', () => {

  const $  = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];

  /* ── TOAST ── */
  const toastEl = $('#lkBuyerToast');
  let toastTimer;
  function showToast(msg, duration = 2400) {
    if (!toastEl) return;
    toastEl.textContent = msg;
    toastEl.classList.add('is-visible');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toastEl.classList.remove('is-visible'), duration);
  }

  /* ── SIDEBAR MOBILE OPEN / CLOSE ── */
  const sidebar   = $('.lk-sidebar');
  const overlay   = $('.lk-overlay');
  const mobileBtn = $('[data-lk-mobile-menu]');

  function openSidebar() {
    sidebar?.classList.add('lk-sidebar-open');
    overlay?.classList.add('lk-overlay-open');
    document.body.classList.add('lk-mobile-sidebar-open');
    document.body.style.overflow = 'hidden';
  }
  function closeSidebar() {
    sidebar?.classList.remove('lk-sidebar-open');
    overlay?.classList.remove('lk-overlay-open');
    document.body.classList.remove('lk-mobile-sidebar-open');
    document.body.style.overflow = '';
  }

  mobileBtn?.addEventListener('click', () => {
    sidebar?.classList.contains('lk-sidebar-open') ? closeSidebar() : openSidebar();
  });
  overlay?.addEventListener('click', closeSidebar);

  /* ── SIDEBAR COLLAPSE (desktop) ── */
  const collapseBtn = $('[data-lk-sidebar-toggle]');
  collapseBtn?.addEventListener('click', () => {
    const isCollapsed = sidebar?.classList.toggle('lk-sidebar-collapsed');
    document.body.classList.toggle('lk-sidebar-collapsed', isCollapsed);
    if (isCollapsed) {
      $$('.lk-nav-submenu.expanded').forEach(sm => closeSubmenu(sm));
    }
  });

  /* ── ACCORDION DROPDOWNS ── */
  function openSubmenu(toggle, submenu) {
    toggle.classList.add('is-open');
    toggle.setAttribute('aria-expanded', 'true');
    submenu.classList.add('expanded');
    const arrow = toggle.querySelector('.lk-nav-arrow');
    if (arrow) arrow.textContent = '\u25b2';
  }

  function closeSubmenu(submenu) {
    submenu.classList.remove('expanded');
    // find the toggle button — walk backwards through siblings
    let toggle = submenu.previousElementSibling;
    while (toggle && !toggle.hasAttribute('data-nav-toggle')) {
      toggle = toggle.previousElementSibling;
    }
    if (!toggle) return;
    toggle.classList.remove('is-open');
    toggle.setAttribute('aria-expanded', 'false');
    const arrow = toggle.querySelector('.lk-nav-arrow');
    if (arrow) arrow.textContent = '\u25bc';
  }

  $$('[data-nav-toggle]').forEach(toggle => {
    // find the submenu — walk forward through siblings
    let submenu = toggle.nextElementSibling;
    while (submenu && !submenu.hasAttribute('data-nav-submenu')) {
      submenu = submenu.nextElementSibling;
    }
    if (!submenu) return;

    toggle.addEventListener('click', e => {
      e.preventDefault();
      e.stopPropagation();
      if (sidebar?.classList.contains('lk-sidebar-collapsed')) return;

      const isOpen = submenu.classList.contains('expanded');
      $$('.lk-nav-submenu.expanded').forEach(other => {
        if (other !== submenu) closeSubmenu(other);
      });
      isOpen ? closeSubmenu(submenu) : openSubmenu(toggle, submenu);
    });
  });

  // Auto-open submenu only when a direct child link is the active page
  $$('.lk-nav-submenu').forEach(submenu => {
    const activeChild = submenu.querySelector('a.is-active');
    if (activeChild) {
      let toggle = submenu.previousElementSibling;
      while (toggle && !toggle.hasAttribute('data-nav-toggle')) {
        toggle = toggle.previousElementSibling;
      }
      if (toggle) openSubmenu(toggle, submenu);
    }
  });

  /* ── ADD TO CART ── */
  let cartCount = parseInt($('[data-cart-badge]')?.textContent || '0', 10) || 0;
  document.addEventListener('click', e => {
    const btn = e.target.closest('[data-add-cart]');
    if (!btn) return;
    e.preventDefault();
    cartCount++;
    $$('[data-cart-badge]').forEach(b => { b.textContent = cartCount; });
    showToast('Added to cart');
  });

  /* ── WISHLIST TOGGLE ── */
  document.addEventListener('click', e => {
    const btn = e.target.closest('[data-wishlist]');
    if (!btn) return;
    e.preventDefault();
    const isActive = btn.classList.toggle('is-active');
    const svg = btn.querySelector('svg');
    if (svg) svg.setAttribute('fill', isActive ? 'currentColor' : 'none');
    btn.setAttribute('aria-pressed', String(isActive));
    showToast(isActive ? 'Added to wishlist' : 'Removed from wishlist');
  });

  /* ── FLASH PICKS COUNTDOWN ── */
  const countdown = $('[data-flash-countdown]');
  if (countdown) {
    const hEl = $('[data-countdown-hours]',   countdown);
    const mEl = $('[data-countdown-minutes]', countdown);
    const sEl = $('[data-countdown-seconds]', countdown);
    let total = (parseInt(hEl?.textContent || '6', 10) * 3600)
              + (parseInt(mEl?.textContent || '24', 10) * 60)
              + parseInt(sEl?.textContent || '18', 10);
    const pad = n => String(n).padStart(2, '0');
    const tick = setInterval(() => {
      if (total <= 0) { clearInterval(tick); return; }
      total--;
      if (hEl) hEl.textContent = pad(Math.floor(total / 3600));
      if (mEl) mEl.textContent = pad(Math.floor((total % 3600) / 60));
      if (sEl) sEl.textContent = pad(total % 60);
    }, 1000);
  }

  /* ── MESSAGES ── */
  const chatMessages = $('[data-chat-messages]');
  if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;

  const messageInput = $('[data-message-input]');
  const sendBtn      = $('[data-send-message]');
  const messageForm  = $('[data-message-form]');

  if (messageInput && sendBtn) {
    messageInput.addEventListener('input', () => {
      sendBtn.disabled = !messageInput.value.trim();
      messageInput.style.height = 'auto';
      messageInput.style.height = Math.min(messageInput.scrollHeight, 112) + 'px';
    });
    messageInput.addEventListener('keydown', e => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        if (!sendBtn.disabled) messageForm?.dispatchEvent(new Event('submit'));
      }
    });
  }

  messageForm?.addEventListener('submit', e => {
    e.preventDefault();
    const text = messageInput?.value.trim();
    if (!text || !chatMessages) return;
    const bubble = document.createElement('div');
    bubble.className = 'flex justify-end';
    bubble.innerHTML = `<div class="max-w-[68%]"><div class="rounded-2xl rounded-br-md bg-amber-600 px-4 py-2.5 text-xs leading-5 text-white shadow-sm">${text}</div><time class="mt-1 block px-1 text-right text-[9px] text-stone-400">Now</time></div>`;
    chatMessages.appendChild(bubble);
    chatMessages.scrollTop = chatMessages.scrollHeight;
    if (messageInput) { messageInput.value = ''; messageInput.style.height = 'auto'; }
    if (sendBtn) sendBtn.disabled = true;
  });

  /* ── CONVERSATION SEARCH ── */
  const convoSearch    = $('[data-conversation-search]');
  const convoList      = $('[data-conversation-list]');
  const convoNoResults = $('[data-conversation-no-results]');
  convoSearch?.addEventListener('input', () => {
    const q = convoSearch.value.trim().toLowerCase();
    let visible = 0;
    $$('[data-conversation-item]', convoList).forEach(item => {
      const show = !q || (item.dataset.conversationName || '').includes(q);
      item.style.display = show ? '' : 'none';
      if (show) visible++;
    });
    convoNoResults?.classList.toggle('hidden', visible > 0 || !q);
  });

  /* ── ORDER SEARCH ── */
  const orderSearch = $('[data-order-search]');
  const orderList   = $('[data-order-list]');
  orderSearch?.addEventListener('input', () => {
    const q = orderSearch.value.trim().toLowerCase();
    $$('[data-order-card]', orderList).forEach(card => {
      card.style.display = !q || (card.dataset.orderSearch || '').toLowerCase().includes(q) ? '' : 'none';
    });
  });

  /* ── SORT / VIEW TOGGLE ── */
  $$('.lk-sort-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      $$('.lk-sort-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
    });
  });
  $$('.lk-view-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      $$('.lk-view-toggle').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
    });
  });

  /* ── ACCOUNT FORM ── */
  $$('[data-account-form]').forEach(form => {
    form.querySelector('button[type="button"]:last-of-type')?.addEventListener('click', () => {
      showToast('Changes saved');
    });
  });

});
