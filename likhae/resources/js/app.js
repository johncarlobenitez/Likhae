/**
 * LIKHAE — Editorial Marketplace Frontend State & Interaction Layer
 */

const LK = (() => {
  const STORAGE_KEY = 'likhae.marketplace.state.v1';

  // Complete catalog of 12 curated Filipino maker products
  const products = {
    1: {
      id: 1,
      slug: 'linen-lounge-set',
      name: 'Linen Lounge Set',
      maker: 'Wear Sundays',
      location: 'Cebu City',
      category: 'Wear',
      price: 1890,
      compareAt: 2290,
      rating: 4.8,
      reviews: 128,
      stock: 4,
      image: 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&w=600&q=80'
    },
    2: {
      id: 2,
      slug: 'handwoven-market-tote',
      name: 'Handwoven Market Tote',
      maker: 'Habi Norte',
      location: 'Benguet',
      category: 'Wear',
      price: 1450,
      compareAt: null,
      rating: 4.9,
      reviews: 86,
      stock: 12,
      image: 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=600&q=80'
    },
    3: {
      id: 3,
      slug: 'sienna-stoneware-mug',
      name: 'Sienna Stoneware Mug',
      maker: 'Clay Story',
      location: 'Quezon City',
      category: 'Live',
      price: 680,
      compareAt: null,
      rating: 4.7,
      reviews: 54,
      stock: 15,
      image: 'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?auto=format&fit=crop&w=600&q=80'
    },
    4: {
      id: 4,
      slug: 'forest-home-diffuser',
      name: 'Forest Home Diffuser',
      maker: 'Amihan Studio',
      location: 'Davao City',
      category: 'Live',
      price: 920,
      compareAt: null,
      rating: 4.9,
      reviews: 204,
      stock: 2,
      image: 'https://images.unsplash.com/photo-1603006905003-be475563bc59?auto=format&fit=crop&w=600&q=80'
    },
    5: {
      id: 5,
      slug: 'marikina-leather-bag',
      name: 'Marikina Leather Day Bag',
      maker: 'Tahi Atelier',
      location: 'Marikina',
      category: 'Wear',
      price: 3490,
      compareAt: 3990,
      rating: 4.8,
      reviews: 73,
      stock: 5,
      image: 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=600&q=80'
    },
    6: {
      id: 6,
      slug: 'mountain-roast',
      name: 'Mountain Roast Coffee Beans',
      maker: 'Cordillera Coffee Co.',
      location: 'Baguio',
      category: 'Taste',
      price: 540,
      compareAt: null,
      rating: 4.9,
      reviews: 318,
      stock: 20,
      image: 'https://images.unsplash.com/photo-1447933601403-0c6688de566e?auto=format&fit=crop&w=600&q=80'
    },
    7: {
      id: 7,
      slug: 'hand-carved-acacia-tray',
      name: 'Hand-Carved Acacia Wood Tray',
      maker: 'Pahiyas Woodcraft',
      location: 'Laguna',
      category: 'Live',
      price: 1120,
      compareAt: null,
      rating: 4.8,
      reviews: 42,
      stock: 8,
      image: 'https://images.unsplash.com/photo-1595425970377-c9703cf48b6d?auto=format&fit=crop&w=600&q=80'
    },
    8: {
      id: 8,
      slug: 'woven-rattan-coaster-set',
      name: 'Woven Rattan Coaster Set',
      maker: 'Isla Living',
      location: 'Bohol',
      category: 'Live',
      price: 480,
      compareAt: null,
      rating: 4.6,
      reviews: 39,
      stock: 18,
      image: 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?auto=format&fit=crop&w=600&q=80'
    },
    9: {
      id: 9,
      slug: 'south-sea-pearl-pendant',
      name: 'South Sea Pearl Artisan Pendant',
      maker: 'Luzon Goldsmiths',
      location: 'Manila',
      category: 'Wear',
      price: 4200,
      compareAt: 4800,
      rating: 4.9,
      reviews: 61,
      stock: 3,
      image: 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?auto=format&fit=crop&w=600&q=80'
    },
    10: {
      id: 10,
      slug: 'sun-drenched-activewear-set',
      name: 'Sun-Drenched Activewear Set',
      maker: 'Araw Movement',
      location: 'Siargao',
      category: 'Move',
      price: 2190,
      compareAt: null,
      rating: 4.8,
      reviews: 97,
      stock: 9,
      image: 'https://images.unsplash.com/photo-1518611012118-696072aa579a?auto=format&fit=crop&w=600&q=80'
    },
    11: {
      id: 11,
      slug: 'hablon-heritage-midi-dress',
      name: 'Hablon Heritage Midi Dress',
      maker: 'Iloilo Loom',
      location: 'Iloilo City',
      category: 'Wear',
      price: 2850,
      compareAt: 3400,
      rating: 4.9,
      reviews: 52,
      stock: 3,
      image: 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?auto=format&fit=crop&w=600&q=80'
    },
    12: {
      id: 12,
      slug: 'botanical-glow-facial-elixir',
      name: 'Botanical Glow Facial Elixir',
      maker: 'Diwa Botanicals',
      location: 'Palawan',
      category: 'Glow',
      price: 1180,
      compareAt: null,
      rating: 4.9,
      reviews: 412,
      stock: 14,
      image: 'https://images.unsplash.com/photo-1608248597359-25f0a8274737?auto=format&fit=crop&w=600&q=80'
    }
  };

  const initialDefaults = {
    cart: [
      { id: 1, color: 'Terracotta', size: 'M', qty: 1, stock: 4, selected: true }
    ],
    wishlist: [2, 5],
    voucher: null,
    recentlyViewed: [1, 2, 6],
    orders: [
      {
        number: 'LKH-2026-0825-1048',
        items: [{ id: 1, color: 'Terracotta', size: 'M', qty: 1 }],
        total: 2010,
        status: 'In Transit',
        createdAt: '2026-08-25T10:48:00Z'
      }
    ],
    messages: []
  };

  const readState = () => {
    try {
      const stored = localStorage.getItem(STORAGE_KEY);
      return stored ? { ...initialDefaults, ...JSON.parse(stored) } : { ...initialDefaults };
    } catch {
      return { ...initialDefaults };
    }
  };

  const writeState = (state) => {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
    } catch (e) {
      console.warn('LocalStorage save error:', e);
    }
  };

  const money = (num) => `₱${Number(num || 0).toLocaleString('en-PH')}`;

  const toast = (msg) => {
    const el = document.querySelector('[data-toast]');
    if (!el) return;
    el.textContent = msg;
    el.hidden = false;
    clearTimeout(el._timer);
    el._timer = setTimeout(() => {
      el.hidden = true;
    }, 2600);
  };

  const isBuyer = () => document.body.dataset.userRole === 'buyer';

  const openModal = (modalEl) => {
    if (!modalEl) return;
    modalEl.hidden = false;
    modalEl.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    const focusable = modalEl.querySelector('button, [href], input, select, textarea');
    if (focusable) focusable.focus();
  };

  const closeModal = (modalEl) => {
    if (!modalEl) return;
    modalEl.hidden = true;
    modalEl.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  };

  const authGate = () => {
    const modal = document.querySelector('[data-auth-gate]');
    openModal(modal);
  };

  const updateCartBadge = () => {
    const state = readState();
    const count = (state.cart || []).reduce((acc, item) => acc + Number(item.qty || 1), 0);
    document.querySelectorAll('[data-cart-count]').forEach(el => {
      el.textContent = count;
    });
  };

  return {
    products,
    readState,
    writeState,
    money,
    toast,
    isBuyer,
    openModal,
    closeModal,
    authGate,
    updateCartBadge
  };
})();

// 1. Global Setup (Navigation, Search, Wishlist Toggles, Auth Gate)
function setupGlobalInteractions() {
  LK.updateCartBadge();

  // Handle restricted clicks for guest users
  document.addEventListener('click', (e) => {
    const gated = e.target.closest('[data-requires-buyer="true"]');
    if (gated) {
      e.preventDefault();
      LK.authGate();
      return;
    }

    if (e.target.closest('[data-close-modal]')) {
      LK.closeModal(document.querySelector('[data-auth-gate]'));
    }
  });

  // Global Escape key dismiss for all open modals
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      document.querySelectorAll('.lk-modal:not([hidden])').forEach(modal => LK.closeModal(modal));
    }
  });

  // Mobile menu drawer
  const menuBtn = document.querySelector('[data-mobile-menu-button]');
  const menuDrawer = document.querySelector('[data-mobile-menu]');
  if (menuBtn && menuDrawer) {
    menuBtn.addEventListener('click', () => {
      menuDrawer.hidden = !menuDrawer.hidden;
    });
  }

  // Password visibility toggle
  document.querySelectorAll('[data-toggle-password]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const parent = e.currentTarget.closest('.lk-password-field');
      const input = parent ? parent.querySelector('input') : document.querySelector('[data-password]');
      if (!input) return;
      input.type = input.type === 'password' ? 'text' : 'password';
      e.currentTarget.textContent = input.type === 'password' ? 'Show' : 'Hide';
    });
  });

  // Live search suggestion popover
  const searchInput = document.querySelector('[data-search-input]');
  const searchSuggestions = document.querySelector('[data-search-suggestions]');
  if (searchInput && searchSuggestions) {
    searchInput.addEventListener('input', () => {
      const q = searchInput.value.trim().toLowerCase();
      if (!q) {
        searchSuggestions.hidden = true;
        return;
      }
      const matches = Object.values(LK.products).filter(p =>
        `${p.name} ${p.maker} ${p.location} ${p.category}`.toLowerCase().includes(q)
      ).slice(0, 5);

      if (matches.length > 0) {
        searchSuggestions.innerHTML = matches.map(p => `
          <a href="/products/${p.slug}">
            <strong>${p.name}</strong>
            <small style="color:var(--slate)"> · ${p.maker} (${p.location}) · ${LK.money(p.price)}</small>
          </a>
        `).join('');
      } else {
        searchSuggestions.innerHTML = `
          <div style="padding:10px;color:var(--slate);font-size:0.85rem">
            No exact matches. Press Enter to search all pieces.
          </div>
        `;
      }
      searchSuggestions.hidden = false;
    });

    document.addEventListener('click', (e) => {
      if (!e.target.closest('[data-search-form]')) {
        searchSuggestions.hidden = true;
      }
    });
  }

  // Wishlist heart button click handlers
  document.querySelectorAll('[data-wishlist-toggle]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      if (!LK.isBuyer()) return;
      const productId = Number(btn.dataset.productId || btn.closest('[data-product-card]')?.dataset.productId || document.querySelector('[data-product-detail]')?.dataset.productId || 1);
      const state = LK.readState();
      const idx = state.wishlist.indexOf(productId);

      if (idx >= 0) {
        state.wishlist.splice(idx, 1);
        btn.classList.remove('is-active');
        btn.textContent = btn.textContent.includes('Wishlist') ? '♡ Add to Wishlist' : '♡';
        LK.toast('Removed from saved wishlist');
      } else {
        state.wishlist.push(productId);
        btn.classList.add('is-active');
        btn.textContent = btn.textContent.includes('Wishlist') ? '♥ Saved to Wishlist' : '♥';
        LK.toast('Saved to wishlist');
      }
      LK.writeState(state);
    });
  });
}

// 2. Product Detail Page (Galleries, Lightbox, Variations, Cart Actions)
function setupProductDetails() {
  const root = document.querySelector('[data-product-detail]');
  if (!root) return;

  const productId = Number(root.dataset.productId || 1);
  let selectedColor = root.querySelector('[data-color.is-active]')?.dataset.color || 'Terracotta';
  let selectedSize = '';
  let currentStock = Number(root.dataset.productStock || 4);
  let currentQty = 1;

  const mainImg = document.querySelector('[data-gallery-main]');
  const countBadge = document.querySelector('[data-gallery-count]');
  const thumbs = document.querySelectorAll('[data-gallery-thumb]');

  // Thumbnail switcher
  thumbs.forEach((thumb, index) => {
    thumb.addEventListener('click', () => {
      thumbs.forEach(t => t.classList.remove('is-active'));
      thumb.classList.add('is-active');
      const img = thumb.querySelector('img');
      if (img && mainImg) mainImg.src = img.src;
      if (countBadge) countBadge.textContent = `${index + 1} / ${thumbs.length}`;
    });
  });

  // Lightbox Zoom Modal
  const galleryOpenBtn = document.querySelector('[data-gallery-open]');
  const galleryModal = document.querySelector('[data-gallery-modal]');
  const lightboxImg = document.querySelector('[data-lightbox-image]');
  if (galleryOpenBtn && galleryModal) {
    galleryOpenBtn.addEventListener('click', () => {
      if (lightboxImg && mainImg) lightboxImg.src = mainImg.src;
      LK.openModal(galleryModal);
    });
  }
  document.querySelectorAll('[data-close-gallery]').forEach(btn => {
    btn.addEventListener('click', () => LK.closeModal(galleryModal));
  });

  // Size Guide Modal
  const sizeGuideBtn = document.querySelector('[data-size-guide]');
  const sizeModal = document.querySelector('[data-size-modal]');
  if (sizeGuideBtn && sizeModal) {
    sizeGuideBtn.addEventListener('click', () => LK.openModal(sizeModal));
  }
  document.querySelectorAll('[data-close-size]').forEach(btn => {
    btn.addEventListener('click', () => LK.closeModal(sizeModal));
  });

  // Color selection
  document.querySelectorAll('[data-color]').forEach(swatch => {
    swatch.addEventListener('click', () => {
      if (swatch.disabled) return;
      document.querySelectorAll('[data-color]').forEach(s => s.classList.remove('is-active'));
      swatch.classList.add('is-active');
      selectedColor = swatch.dataset.color;
      currentStock = Number(swatch.dataset.stock || 4);

      const colorLabel = document.querySelector('[data-color-label]');
      if (colorLabel) colorLabel.textContent = selectedColor;

      const stockCopy = document.querySelector('[data-stock-copy]');
      if (stockCopy) {
        stockCopy.textContent = currentStock <= 3
          ? `Only ${currentStock} left in this variation`
          : `In stock · ${currentStock} available`;
      }

      const availCopy = document.querySelector('[data-available-copy]');
      if (availCopy) availCopy.textContent = `${currentStock} pieces available`;

      currentQty = Math.min(currentQty, currentStock);
      const qtyInput = document.querySelector('[data-qty]');
      if (qtyInput) qtyInput.value = currentQty;
    });
  });

  // Size selection
  const sizeButtons = document.querySelectorAll('[data-size]');
  sizeButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      if (btn.disabled) return;
      sizeButtons.forEach(b => b.classList.remove('is-active'));
      btn.classList.add('is-active');
      selectedSize = btn.dataset.size;

      const err = document.querySelector('[data-variation-error]');
      if (err) err.textContent = '';
    });
  });

  // Quantity controls
  const qtyInput = document.querySelector('[data-qty]');
  const minusBtn = document.querySelector('[data-qty-minus]');
  const plusBtn = document.querySelector('[data-qty-plus]');

  if (minusBtn && qtyInput) {
    minusBtn.addEventListener('click', () => {
      currentQty = Math.max(1, currentQty - 1);
      qtyInput.value = currentQty;
    });
  }
  if (plusBtn && qtyInput) {
    plusBtn.addEventListener('click', () => {
      currentQty = Math.min(currentStock, currentQty + 1);
      qtyInput.value = currentQty;
    });
  }

  // Add to Bag
  const addCartBtn = document.querySelector('[data-add-cart]');
  if (addCartBtn) {
    addCartBtn.addEventListener('click', () => {
      if (!LK.isBuyer()) return;
      if (sizeButtons.length > 0 && !selectedSize) {
        const err = document.querySelector('[data-variation-error]');
        if (err) err.textContent = 'Please choose a size before adding to bag.';
        return;
      }

      const state = LK.readState();
      const existing = (state.cart || []).find(
        item => item.id === productId && item.color === selectedColor && item.size === selectedSize
      );

      if (existing) {
        existing.qty = Math.min(currentStock, existing.qty + currentQty);
      } else {
        state.cart = state.cart || [];
        state.cart.push({
          id: productId,
          color: selectedColor,
          size: selectedSize,
          qty: currentQty,
          stock: currentStock,
          selected: true
        });
      }

      LK.writeState(state);
      LK.updateCartBadge();
      LK.toast('Added to your shopping bag');
    });
  }

  // Buy Now
  const buyNowBtn = document.querySelector('[data-buy-now]');
  if (buyNowBtn) {
    buyNowBtn.addEventListener('click', () => {
      if (!LK.isBuyer()) return;
      if (sizeButtons.length > 0 && !selectedSize) {
        const err = document.querySelector('[data-variation-error]');
        if (err) err.textContent = 'Please choose a size before checking out.';
        return;
      }
      if (addCartBtn) addCartBtn.click();
      setTimeout(() => {
        window.location.href = '/buyer/checkout';
      }, 200);
    });
  }
}

// 3. Shopping Bag & Voucher Engine
function setupCart() {
  const root = document.querySelector('[data-cart-page]');
  if (!root) return;

  const renderCart = () => {
    const state = LK.readState();
    const listWrap = root.querySelector('[data-cart-items]');
    const emptyState = root.querySelector('[data-cart-empty]');
    const selectAllCheckbox = root.querySelector('[data-cart-select-all]');

    if (!listWrap) return;
    listWrap.innerHTML = '';

    const cart = state.cart || [];
    if (emptyState) emptyState.hidden = cart.length > 0;

    cart.forEach((item, index) => {
      const prod = LK.products[item.id] || LK.products[1];
      const row = document.createElement('article');
      row.className = 'lk-cart-item';
      row.innerHTML = `
        <input type="checkbox" ${item.selected !== false ? 'checked' : ''} data-cart-select="${index}" aria-label="Select ${prod.name}">
        <img src="${prod.image}" alt="${prod.name}">
        <div class="lk-cart-item__meta">
          <strong>${prod.name}</strong>
          <span>${item.color || 'Standard'}${item.size ? ' / ' + item.size : ''}</span>
          <span>${prod.maker} · ${prod.location}</span>
          <div class="lk-cart-item__actions">
            <div class="lk-quantity">
              <button type="button" data-cart-minus="${index}" aria-label="Decrease quantity">−</button>
              <input value="${item.qty}" readonly aria-label="Quantity">
              <button type="button" data-cart-plus="${index}" aria-label="Increase quantity">+</button>
            </div>
            <button type="button" class="lk-text-link" data-cart-remove="${index}" style="color:var(--coral);font-size:0.85rem">Remove</button>
          </div>
        </div>
        <strong style="font-size:1.1rem">${LK.money(prod.price * item.qty)}</strong>
      `;
      listWrap.appendChild(row);
    });

    const selectedItems = cart.filter(i => i.selected !== false);
    const subtotal = selectedItems.reduce((sum, item) => {
      const p = LK.products[item.id] || LK.products[1];
      return sum + (p.price * item.qty);
    }, 0);

    let shipping = selectedItems.length ? 120 : 0;
    let discount = 0;

    if (state.voucher === 'LIKHAE100') discount = Math.min(100, subtotal);
    if (state.voucher === 'LOCAL10') discount = Math.round(subtotal * 0.1);
    if (state.voucher === 'FREESHIP') shipping = 0;

    const countLabel = root.querySelector('[data-selected-count]');
    if (countLabel) countLabel.textContent = `${selectedItems.length} of ${cart.length} items selected`;

    const subtotalEl = root.querySelector('[data-cart-subtotal]');
    if (subtotalEl) subtotalEl.textContent = LK.money(subtotal);

    const shippingEl = root.querySelector('[data-cart-shipping]');
    if (shippingEl) shippingEl.textContent = LK.money(shipping);

    const discountEl = root.querySelector('[data-cart-discount]');
    if (discountEl) discountEl.textContent = `−${LK.money(discount)}`;

    const totalEl = root.querySelector('[data-cart-total]');
    if (totalEl) totalEl.textContent = LK.money(Math.max(0, subtotal + shipping - discount));

    if (selectAllCheckbox) {
      selectAllCheckbox.checked = cart.length > 0 && selectedItems.length === cart.length;
    }

    LK.updateCartBadge();
  };

  // Quantity and remove actions
  root.addEventListener('click', (e) => {
    const state = LK.readState();
    if (e.target.dataset.cartMinus !== undefined) {
      const idx = Number(e.target.dataset.cartMinus);
      state.cart[idx].qty = Math.max(1, state.cart[idx].qty - 1);
      LK.writeState(state);
      renderCart();
    }
    if (e.target.dataset.cartPlus !== undefined) {
      const idx = Number(e.target.dataset.cartPlus);
      const stock = state.cart[idx].stock || 99;
      state.cart[idx].qty = Math.min(stock, state.cart[idx].qty + 1);
      LK.writeState(state);
      renderCart();
    }
    if (e.target.dataset.cartRemove !== undefined) {
      const idx = Number(e.target.dataset.cartRemove);
      state.cart.splice(idx, 1);
      LK.writeState(state);
      LK.toast('Item removed from shopping bag');
      renderCart();
    }
  });

  // Checkbox selections
  root.addEventListener('change', (e) => {
    const state = LK.readState();
    if (e.target.dataset.cartSelect !== undefined) {
      const idx = Number(e.target.dataset.cartSelect);
      state.cart[idx].selected = e.target.checked;
      LK.writeState(state);
      renderCart();
    }
    if (e.target.matches('[data-cart-select-all]')) {
      (state.cart || []).forEach(item => { item.selected = e.target.checked; });
      LK.writeState(state);
      renderCart();
    }
  });

  // Apply Voucher
  const applyVoucherBtn = root.querySelector('[data-apply-voucher]');
  const voucherInput = root.querySelector('[data-voucher-code]');
  const voucherMsg = root.querySelector('[data-voucher-message]');

  if (applyVoucherBtn && voucherInput) {
    applyVoucherBtn.addEventListener('click', () => {
      const code = voucherInput.value.trim().toUpperCase();
      const state = LK.readState();
      if (['LIKHAE100', 'LOCAL10', 'FREESHIP'].includes(code)) {
        state.voucher = code;
        LK.writeState(state);
        if (voucherMsg) {
          voucherMsg.textContent = `✓ Voucher "${code}" applied successfully!`;
          voucherMsg.style.color = '#2e6930';
        }
        LK.toast(`Voucher ${code} applied`);
        renderCart();
      } else {
        if (voucherMsg) {
          voucherMsg.textContent = 'Invalid or expired voucher code. Try LIKHAE100, LOCAL10, or FREESHIP.';
          voucherMsg.style.color = 'var(--coral)';
        }
      }
    });
  }

  renderCart();
}

// 4. Checkout & Order Placement
function setupCheckout() {
  const root = document.querySelector('[data-checkout-page]');
  if (!root) return;

  const renderCheckout = () => {
    const state = LK.readState();
    const items = (state.cart || []).filter(i => i.selected !== false);
    const wrap = root.querySelector('[data-checkout-items]');

    if (wrap) {
      if (items.length > 0) {
        wrap.innerHTML = items.map(item => {
          const prod = LK.products[item.id] || LK.products[1];
          return `
            <div class="lk-order-product" style="border-bottom:1px solid var(--line);padding:14px 0">
              <img src="${prod.image}" alt="${prod.name}">
              <div>
                <strong>${prod.name}</strong>
                <span style="color:var(--slate);font-size:0.85rem">${item.color || 'Standard'}${item.size ? ' / ' + item.size : ''} · Qty ${item.qty}</span>
                <span style="font-size:0.8rem;color:var(--slate)">Sold by ${prod.maker}</span>
                <b style="margin-top:4px;display:block">${LK.money(prod.price * item.qty)}</b>
              </div>
            </div>
          `;
        }).join('');
      } else {
        wrap.innerHTML = '<p style="color:var(--slate)">No items selected for checkout. <a href="/buyer/cart" class="lk-text-link">Return to bag</a></p>';
      }
    }

    const subtotal = items.reduce((sum, item) => {
      const p = LK.products[item.id] || LK.products[1];
      return sum + (p.price * item.qty);
    }, 0);

    const shippingOption = document.querySelector('[data-shipping-option]:checked');
    let shipFee = shippingOption ? Number(shippingOption.dataset.fee || 120) : 120;

    let discount = 0;
    if (state.voucher === 'LIKHAE100') discount = Math.min(100, subtotal);
    if (state.voucher === 'LOCAL10') discount = Math.round(subtotal * 0.1);
    if (state.voucher === 'FREESHIP') shipFee = 0;

    const subEl = root.querySelector('[data-checkout-subtotal]');
    if (subEl) subEl.textContent = LK.money(subtotal);

    const shipEl = root.querySelector('[data-checkout-shipping]');
    if (shipEl) shipEl.textContent = LK.money(shipFee);

    const discEl = root.querySelector('[data-checkout-discount]');
    if (discEl) discEl.textContent = `−${LK.money(discount)}`;

    const totalEl = root.querySelector('[data-checkout-total]');
    if (totalEl) totalEl.textContent = LK.money(Math.max(0, subtotal + shipFee - discount));
  };

  root.querySelectorAll('[data-shipping-option]').forEach(opt => {
    opt.addEventListener('change', renderCheckout);
  });

  const placeOrderBtn = root.querySelector('[data-place-order]');
  const successModal = document.querySelector('[data-order-success]');
  const orderNumEl = document.querySelector('[data-order-number]');

  if (placeOrderBtn) {
    placeOrderBtn.addEventListener('click', () => {
      const state = LK.readState();
      const selected = (state.cart || []).filter(i => i.selected !== false);

      if (!selected.length) {
        LK.toast('Your shopping bag has no selected items to checkout.');
        return;
      }

      const today = new Date();
      const y = today.getFullYear();
      const m = String(today.getMonth() + 1).padStart(2, '0');
      const d = String(today.getDate()).padStart(2, '0');
      const rand = Math.floor(1000 + Math.random() * 9000);
      const orderNo = `LKH-${y}-${m}${d}-${rand}`;

      state.orders = state.orders || [];
      state.orders.unshift({
        number: orderNo,
        items: selected,
        status: 'To Ship',
        createdAt: new Date().toISOString()
      });

      // Clear checked out items
      state.cart = (state.cart || []).filter(i => i.selected === false);
      state.voucher = null;
      LK.writeState(state);

      if (orderNumEl) orderNumEl.textContent = orderNo;
      LK.openModal(successModal);
      LK.updateCartBadge();
      LK.toast('Order placed successfully!');
    });
  }

  renderCheckout();
}

// 5. Wishlist Page Setup
function setupWishlist() {
  const root = document.querySelector('[data-wishlist-page]');
  if (!root) return;

  const state = LK.readState();
  const wrap = root.querySelector('[data-wishlist-items]');
  const emptyState = root.querySelector('[data-wishlist-empty]');

  if (!wrap) return;
  const list = state.wishlist || [];

  if (emptyState) emptyState.hidden = list.length > 0;

  wrap.innerHTML = list.map(id => {
    const prod = LK.products[id] || LK.products[1];
    return `
      <article class="lk-product-card" data-product-card data-product-id="${prod.id}">
        <a class="lk-product-card__media" href="/buyer/products/${prod.slug}">
          <img src="${prod.image}" alt="${prod.name}">
        </a>
        <div class="lk-product-card__meta">
          <div>
            <p class="lk-eyebrow">${prod.maker} · ${prod.location}</p>
            <h3><a href="/buyer/products/${prod.slug}">${prod.name}</a></h3>
            <div class="lk-price-row">
              <strong>${LK.money(prod.price)}</strong>
            </div>
            <p class="lk-rating">★ ${prod.rating} <span>(${prod.reviews})</span></p>
          </div>
          <button class="lk-heart is-active" type="button" aria-label="Remove from wishlist" data-wishlist-remove="${prod.id}">♥</button>
        </div>
      </article>
    `;
  }).join('');

  root.addEventListener('click', (e) => {
    if (e.target.dataset.wishlistRemove) {
      const idToRemove = Number(e.target.dataset.wishlistRemove);
      const s = LK.readState();
      s.wishlist = (s.wishlist || []).filter(x => x !== idToRemove);
      LK.writeState(s);
      LK.toast('Removed from saved wishlist');
      setupWishlist();
    }
  });
}

// 6. Multi-Step Registration Wizard & Philippine Address Selectors
function setupRegistration() {
  const root = document.querySelector('[data-registration]');
  if (!root) return;

  let currentStep = 1;
  const form = root.querySelector('[data-registration-form]');
  const errorBanner = root.querySelector('[data-registration-error]');
  const nextBtn = root.querySelector('[data-next-step]');
  const prevBtn = root.querySelector('[data-prev-step]');
  const submitBtn = root.querySelector('[data-submit-registration]');
  const cancelLink = root.querySelector('[data-cancel-link]');
  const confirmCheckbox = root.querySelector('[data-confirm]');

  // Philippine Locations Hierarchy
  const locations = {
    'Cebu': {
      'Cebu City': ['Lahug', 'Mabolo', 'Guadalupe', 'Banilad', 'Kasambagan', 'Talamban'],
      'Mandaue City': ['Banilad', 'Subangdaku', 'Tipolo', 'Bakilid', 'Alang-Alang'],
      'Lapu-Lapu City': ['Basak', 'Maribago', 'Mactan', 'Punta Engaño']
    },
    'Benguet': {
      'Baguio City': ['Session Road Area', 'Loakan', 'Bakakeng', 'Camp 7', 'Mines View'],
      'La Trinidad': ['Poblacion', 'Balili', 'Km. 5', 'Puguis'],
      'Atok': ['Poblacion', 'Sayangan', 'Calasipan']
    },
    'Metro Manila': {
      'Marikina City': ['Concepcion Uno', 'San Roque', 'Sto. Niño', 'Barangka', 'Jesus Dela Peña'],
      'City of Manila': ['Ermita', 'Malate', 'Sampaloc', 'Intramuros', 'Binondo'],
      'Makati City': ['Poblacion', 'San Lorenzo', 'Bel-Air', 'Legazpi Village'],
      'Quezon City': ['Diliman', 'New Manila', 'Katipunan', 'Cubao']
    },
    'Davao del Sur': {
      'Davao City': ['Buhangin', 'Matina', 'Talomo', 'Poblacion District', 'Agdao']
    },
    'Iloilo': {
      'Iloilo City': ['Mandurriao', 'Jaro', 'La Paz', 'Molo', 'City Proper'],
      'Miag-ao': ['Poblacion', 'Baybay', 'Damilisan', 'Guibongan']
    },
    'Laguna': {
      'Paete': ['Bagumbayan', 'Quinale', 'Ilaya'],
      'Los Baños': ['Batong Malake', 'College', 'San Antonio']
    },
    'Bohol': {
      'Tagbilaran City': ['Cogon', 'Poblacion I', 'Taloto'],
      'Antequera': ['Poblacion', 'Can-omay', 'Villa Aurora']
    },
    'Surigao del Norte': {
      'General Luna (Siargao)': ['Poblacion', 'Catangnan', 'Cloud 9', 'General Luna']
    },
    'Palawan': {
      'Puerto Princesa City': ['San Pedro', 'Bancao-Bancao', 'Santa Monica', 'Tiniguiban']
    }
  };

  const provinceSelect = root.querySelector('[data-province]');
  const municipalitySelect = root.querySelector('[data-municipality]');
  const barangaySelect = root.querySelector('[data-barangay]');

  if (provinceSelect) {
    Object.keys(locations).forEach(prov => {
      provinceSelect.add(new Option(prov, prov));
    });

    provinceSelect.addEventListener('change', () => {
      municipalitySelect.innerHTML = '<option value="">Choose city</option>';
      barangaySelect.innerHTML = '<option value="">Choose barangay</option>';
      municipalitySelect.disabled = !provinceSelect.value;
      barangaySelect.disabled = true;

      if (provinceSelect.value && locations[provinceSelect.value]) {
        Object.keys(locations[provinceSelect.value]).forEach(city => {
          municipalitySelect.add(new Option(city, city));
        });
      }
    });

    municipalitySelect.addEventListener('change', () => {
      barangaySelect.innerHTML = '<option value="">Choose barangay</option>';
      barangaySelect.disabled = !municipalitySelect.value;

      if (municipalitySelect.value && locations[provinceSelect.value]?.[municipalitySelect.value]) {
        locations[provinceSelect.value][municipalitySelect.value].forEach(brgy => {
          barangaySelect.add(new Option(brgy, brgy));
        });
      }
    });
  }

  // Real-time Age Calculation
  const birthdayInput = root.querySelector('[data-birthday]');
  const ageInput = root.querySelector('[data-age]');
  if (birthdayInput && ageInput) {
    birthdayInput.addEventListener('change', () => {
      const birth = new Date(birthdayInput.value);
      if (Number.isNaN(birth.getTime())) {
        ageInput.value = '';
        return;
      }
      const now = new Date();
      let age = now.getFullYear() - birth.getFullYear();
      const monthDiff = now.getMonth() - birth.getMonth();
      if (monthDiff < 0 || (monthDiff === 0 && now.getDate() < birth.getDate())) {
        age--;
      }
      ageInput.value = age >= 0 ? `${age} years old` : '';
    });
  }

  // ID Upload handling & preview
  const uploadInput = root.querySelector('[data-id-upload]');
  const uploadPreview = root.querySelector('[data-upload-preview]');
  const uploadName = root.querySelector('[data-upload-name]');
  const uploadSize = root.querySelector('[data-upload-size]');
  const uploadImg = root.querySelector('[data-upload-image]');
  const removeUploadBtn = root.querySelector('[data-remove-upload]');

  if (uploadInput) {
    uploadInput.addEventListener('change', () => {
      const file = uploadInput.files[0];
      if (!file) return;

      if (file.size > 5 * 1024 * 1024) {
        if (errorBanner) errorBanner.textContent = 'Selected file exceeds the maximum 5 MB limit.';
        uploadInput.value = '';
        return;
      }

      if (errorBanner) errorBanner.textContent = '';
      if (uploadPreview) uploadPreview.hidden = false;
      if (uploadName) uploadName.textContent = file.name;
      if (uploadSize) uploadSize.textContent = `${(file.size / (1024 * 1024)).toFixed(2)} MB`;

      if (uploadImg) {
        if (file.type.startsWith('image/')) {
          uploadImg.src = URL.createObjectURL(file);
          uploadImg.hidden = false;
        } else {
          uploadImg.hidden = true;
        }
      }
    });
  }

  if (removeUploadBtn && uploadInput && uploadPreview) {
    removeUploadBtn.addEventListener('click', () => {
      uploadInput.value = '';
      uploadPreview.hidden = true;
    });
  }

  // Step Switcher
  const updateStepView = () => {
    root.querySelectorAll('[data-step]').forEach(el => {
      el.classList.toggle('is-active', Number(el.dataset.step) === currentStep);
    });
    root.querySelectorAll('[data-step-indicator]').forEach(el => {
      el.classList.toggle('is-active', Number(el.dataset.stepIndicator) === currentStep);
    });

    if (prevBtn) prevBtn.hidden = currentStep === 1;
    if (cancelLink) cancelLink.hidden = currentStep > 1;
    if (nextBtn) nextBtn.hidden = currentStep === 4;
    if (submitBtn) submitBtn.hidden = currentStep !== 4;

    if (currentStep === 4) {
      const fd = new FormData(form);
      const summaryContainer = root.querySelector('[data-registration-summary]');
      if (summaryContainer) {
        summaryContainer.innerHTML = `
          <section style="padding:16px;border:1px solid var(--line);border-radius:10px;background:var(--paper);margin-bottom:12px">
            <strong style="color:var(--coral);font-size:0.8rem;text-transform:uppercase;letter-spacing:0.1em;display:block;margin-bottom:6px">1. Personal Details</strong>
            <p style="margin:0;font-size:0.95rem">
              <strong>${fd.get('first_name') || ''} ${fd.get('middle_initial') || ''} ${fd.get('last_name') || ''}</strong><br>
              <span style="color:var(--slate)">Sex: ${fd.get('sex') || '—'} · Birthday: ${fd.get('birthday') || '—'} (${fd.get('age') || ''})</span>
            </p>
          </section>
          <section style="padding:16px;border:1px solid var(--line);border-radius:10px;background:var(--paper);margin-bottom:12px">
            <strong style="color:var(--coral);font-size:0.8rem;text-transform:uppercase;letter-spacing:0.1em;display:block;margin-bottom:6px">2. Contact & Address</strong>
            <p style="margin:0;font-size:0.95rem">
              <strong>${fd.get('email') || ''}</strong> · <span style="color:var(--slate)">+63 ${fd.get('contact') || ''}</span><br>
              <span style="color:var(--slate)">${fd.get('house') ? fd.get('house') + ', ' : ''}${fd.get('street') || ''}, Brgy. ${fd.get('barangay') || ''}, ${fd.get('municipality') || ''}, ${fd.get('province') || ''} ${fd.get('postal') || ''}</span>
            </p>
          </section>
          <section style="padding:16px;border:1px solid var(--line);border-radius:10px;background:var(--paper)">
            <strong style="color:var(--coral);font-size:0.8rem;text-transform:uppercase;letter-spacing:0.1em;display:block;margin-bottom:6px">3. Verification Document</strong>
            <p style="margin:0;font-size:0.95rem;color:var(--slate)">
              📄 ${uploadName?.textContent || 'Government ID attached'}
            </p>
          </section>
        `;
      }
    }
  };

  if (confirmCheckbox && submitBtn) {
    confirmCheckbox.addEventListener('change', () => {
      submitBtn.disabled = !confirmCheckbox.checked;
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', () => {
      const stepEl = root.querySelector(`[data-step="${currentStep}"]`);
      const requiredInputs = [...(stepEl ? stepEl.querySelectorAll('[required]') : [])];

      const missing = requiredInputs.find(input => !input.value.trim());
      if (missing) {
        if (errorBanner) errorBanner.textContent = 'Please fill out all required fields in this step before continuing.';
        missing.focus();
        return;
      }

      if (currentStep === 3) {
        if (!uploadInput || !uploadInput.files.length) {
          if (errorBanner) errorBanner.textContent = 'Please select a valid ID document file.';
          return;
        }
      }

      if (errorBanner) errorBanner.textContent = '';
      currentStep = Math.min(4, currentStep + 1);
      updateStepView();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  if (prevBtn) {
    prevBtn.addEventListener('click', () => {
      currentStep = Math.max(1, currentStep - 1);
      if (errorBanner) errorBanner.textContent = '';
      updateStepView();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  updateStepView();
}

// 7. Extra Pages (Messages chat sending, Notifications mark read, Reviews star picker, Account forms)
function setupExtraFeatures() {
  // Live Chat send
  const chatForm = document.querySelector('[data-chat-form]');
  const chatInput = document.querySelector('[data-chat-input]');
  const chatThread = document.querySelector('[data-chat-thread]');

  if (chatForm && chatInput && chatThread) {
    chatForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const val = chatInput.value.trim();
      if (!val) return;

      const now = new Date();
      const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

      const msg = document.createElement('div');
      msg.className = 'lk-message lk-message--me';
      msg.innerHTML = `${val.replace(/[<>]/g, '')}<small>${timeStr}</small>`;
      chatThread.appendChild(msg);

      chatInput.value = '';
      chatThread.scrollTop = chatThread.scrollHeight;
      LK.toast('Message sent to maker');
    });
  }

  // Notifications mark read
  document.querySelectorAll('[data-mark-read]').forEach(btn => {
    btn.addEventListener('click', () => {
      const card = btn.closest('[data-notification]');
      if (card) card.classList.remove('is-unread');
    });
  });

  const markAllBtn = document.querySelector('[data-mark-all-read]');
  if (markAllBtn) {
    markAllBtn.addEventListener('click', () => {
      document.querySelectorAll('[data-notification]').forEach(n => n.classList.remove('is-unread'));
      LK.toast('All notifications marked as read');
    });
  }

  // Review star picker
  document.querySelectorAll('[data-star]').forEach(starBtn => {
    starBtn.addEventListener('click', () => {
      const rating = Number(starBtn.dataset.star || 5);
      document.querySelectorAll('[data-star]').forEach(s => {
        const val = Number(s.dataset.star);
        s.textContent = val <= rating ? '★' : '☆';
      });
    });
  });

  // Review form submit
  const reviewForm = document.querySelector('[data-review-form]');
  if (reviewForm) {
    reviewForm.addEventListener('submit', (e) => {
      e.preventDefault();
      LK.toast('Review submitted! Thank you for supporting the maker.');
      setTimeout(() => {
        window.location.href = '/buyer/orders';
      }, 1500);
    });
  }

  // Profile update form
  const profileForm = document.querySelector('[data-profile-form]');
  if (profileForm) {
    profileForm.addEventListener('submit', (e) => {
      e.preventDefault();
      LK.toast('Profile updated successfully');
    });
  }

  // Password update form
  const passForm = document.querySelector('[data-password-form]');
  if (passForm) {
    passForm.addEventListener('submit', (e) => {
      e.preventDefault();
      LK.toast('Password updated successfully');
      passForm.reset();
    });
  }

  // Toggle new address form
  document.querySelectorAll('[data-toggle-new-address]').forEach(btn => {
    btn.addEventListener('click', () => {
      const addressForm = document.querySelector('[data-new-address-form]');
      if (addressForm) addressForm.hidden = !addressForm.hidden;
    });
  });

  const addAddressForm = document.querySelector('[data-add-address-form]');
  if (addAddressForm) {
    addAddressForm.addEventListener('submit', (e) => {
      e.preventDefault();
      LK.toast('New address saved');
      const addressForm = document.querySelector('[data-new-address-form]');
      if (addressForm) addressForm.hidden = true;
    });
  }
}

// Initialize everything on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
  setupGlobalInteractions();
  setupProductDetails();
  setupCart();
  setupWishlist();
  setupCheckout();
  setupRegistration();
  setupExtraFeatures();
});
