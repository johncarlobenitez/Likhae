<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>My Addresses — LIKHAE</title>

    @vite([
        'resources/css/buyer/addresses.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-[#f5f2ed] text-[#111111] antialiased">

@php
    /*
    |--------------------------------------------------------------------------
    | Demo Buyer / Address Data
    |--------------------------------------------------------------------------
    | Replace with authenticated buyer + addresses from your controller.
    */
    $buyer = [
        'first_name' => auth()->check() ? explode(' ', auth()->user()->name)[0] : 'Juan',
        'cart_count' => 2,
        'message_count' => 3,
        'notification_count' => 4,
    ];

    $addresses = [
        [
            'id' => 1,
            'label' => 'Home',
            'recipient' => 'Juan Dela Cruz',
            'phone' => '0917 123 4567',
            'house_number' => '123',
            'street' => 'Rizal Street',
            'barangay' => 'San Antonio',
            'municipality' => 'Makati City',
            'province' => 'Metro Manila',
            'postal_code' => '1203',
            'details' => 'Near the barangay hall',
            'is_default' => true,
        ],
        [
            'id' => 2,
            'label' => 'Work',
            'recipient' => 'Juan Dela Cruz',
            'phone' => '0917 123 4567',
            'house_number' => '8F Unit 802',
            'street' => 'Ayala Avenue',
            'barangay' => 'Bel-Air',
            'municipality' => 'Makati City',
            'province' => 'Metro Manila',
            'postal_code' => '1227',
            'details' => 'Reception desk, Monday to Friday',
            'is_default' => false,
        ],
    ];
@endphp

<header class="sticky top-0 z-50 border-b border-black/10 bg-white/95 backdrop-blur">
    <div class="likhae-container">
        <div class="flex h-16 items-center gap-4">
            <a href="{{ url('/buyer/home') }}" class="flex shrink-0 items-center gap-2">
                <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black text-white">L</span>
                <span class="text-xl font-black tracking-tight">LIKHAE</span>
            </a>

            <form action="{{ url('/buyer/products') }}" method="GET" class="hidden min-w-0 flex-1 md:flex">
                <div class="flex h-11 w-full overflow-hidden border border-[#dedad3] bg-white">
                    <input
                        type="search"
                        name="q"
                        placeholder="Search products, brands, Filipino finds..."
                        class="min-w-0 flex-1 bg-transparent px-4 text-sm outline-none placeholder:text-[#b9b4ad]"
                    >

                    <button
                        type="submit"
                        class="flex w-[108px] items-center justify-center gap-2 bg-[#d92d2f] px-4 text-sm font-bold text-white transition hover:bg-[#bd2024]"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-3.5-3.5"></path>
                        </svg>
                        Search
                    </button>
                </div>
            </form>

            <nav class="ml-auto flex items-center gap-1 sm:gap-2">
                <a href="{{ url('/buyer/notifications') }}" class="buyer-header-action">
                    <span class="relative">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                            <path d="M10 21h4"></path>
                        </svg>
                        <span class="header-count">{{ $buyer['notification_count'] }}</span>
                    </span>
                    <span class="hidden text-[10px] lg:block">Alerts</span>
                </a>

                <a href="{{ url('/buyer/messages') }}" class="buyer-header-action">
                    <span class="relative">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M4 5h16v11H8l-4 4V5Z"></path>
                        </svg>
                        <span class="header-count">{{ $buyer['message_count'] }}</span>
                    </span>
                    <span class="hidden text-[10px] lg:block">Messages</span>
                </a>

                <a href="{{ url('/buyer/cart') }}" class="buyer-header-action">
                    <span class="relative">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M3 4h2l2.1 10.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L20 7H6"></path>
                            <circle cx="9" cy="20" r="1"></circle>
                            <circle cx="17" cy="20" r="1"></circle>
                        </svg>
                        <span class="header-count">{{ $buyer['cart_count'] }}</span>
                    </span>
                    <span class="hidden text-[10px] lg:block">Cart</span>
                </a>

                <a href="{{ url('/buyer/account') }}" class="ml-1 flex items-center gap-2 border-l border-[#ece7e0] pl-3">
                    <span class="grid h-8 w-8 place-items-center rounded-full bg-[#111] text-xs font-black text-white">
                        {{ strtoupper(substr($buyer['first_name'], 0, 1)) }}
                    </span>
                    <span class="hidden xl:block">
                        <span class="block text-[11px] font-bold">{{ $buyer['first_name'] }}</span>
                        <span class="block text-[9px] text-[#a39c94]">Buyer</span>
                    </span>
                </a>
            </nav>
        </div>
    </div>
</header>

<main>
    <section class="border-b border-[#ded8d0] bg-white">
        <div class="likhae-container py-8">
            <div class="text-[11px] text-[#9b958d]">
                <a href="{{ url('/buyer/home') }}" class="transition hover:text-[#d92d2f]">Home</a>
                <span class="mx-2">/</span>
                <a href="{{ url('/buyer/account') }}" class="transition hover:text-[#d92d2f]">My Account</a>
                <span class="mx-2">/</span>
                <span class="text-[#4d4944]">Addresses</span>
            </div>

            <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="buyer-section-eyebrow">DELIVERY DETAILS</p>
                    <h1 class="mt-3 text-3xl font-black tracking-[-0.04em] sm:text-4xl">My Addresses</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-[#8b847c]">
                        Manage the addresses you use for checkout and deliveries.
                    </p>
                </div>

                <button id="addAddressButton" type="button" class="primary-action">
                    + Add New Address
                </button>
            </div>
        </div>
    </section>

    <section class="py-8 lg:py-12">
        <div class="likhae-container">
            <div class="grid gap-8 lg:grid-cols-[260px_minmax(0,1fr)]">
                <aside>
                    <nav class="account-nav-card">
                        <a href="{{ url('/buyer/account') }}" class="account-menu-item">
                            <span>Profile</span>
                        </a>
                        <a href="{{ url('/buyer/account/addresses') }}" class="account-menu-item is-active">
                            <span>Addresses</span>
                        </a>
                        <a href="{{ url('/buyer/orders') }}" class="account-menu-item">
                            <span>My Orders</span>
                        </a>
                        <a href="{{ url('/buyer/wishlist') }}" class="account-menu-item">
                            <span>Wishlist</span>
                        </a>
                        <a href="{{ url('/buyer/messages') }}" class="account-menu-item">
                            <span>Messages</span>
                        </a>
                        <a href="{{ url('/buyer/account/security') }}" class="account-menu-item">
                            <span>Security</span>
                        </a>
                    </nav>
                </aside>

                <div class="space-y-5">
                    @forelse($addresses as $address)
                        <article class="address-card">
                            <div class="flex flex-col gap-5 p-5 sm:p-6">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="flex items-start gap-4">
                                        <div class="grid h-11 w-11 shrink-0 place-items-center border border-[#ddd6ce] bg-[#faf8f5] text-[#d92d2f]">
                                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                                                <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z"></path>
                                                <circle cx="12" cy="10" r="2"></circle>
                                            </svg>
                                        </div>

                                        <div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h2 class="text-sm font-black">{{ $address['label'] }}</h2>

                                                @if($address['is_default'])
                                                    <span class="default-badge">DEFAULT</span>
                                                @endif
                                            </div>

                                            <p class="mt-3 text-sm font-bold text-[#4d4944]">
                                                {{ $address['recipient'] }}
                                            </p>

                                            <p class="mt-1 text-xs text-[#79726b]">
                                                {{ $address['phone'] }}
                                            </p>

                                            <p class="mt-3 max-w-2xl text-sm leading-6 text-[#5f5953]">
                                                {{ $address['house_number'] }} {{ $address['street'] }},
                                                Barangay {{ $address['barangay'] }},
                                                {{ $address['municipality'] }},
                                                {{ $address['province'] }}
                                                {{ $address['postal_code'] }}
                                            </p>

                                            @if($address['details'])
                                                <p class="mt-2 text-[10px] text-[#9b948c]">
                                                    {{ $address['details'] }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            type="button"
                                            class="secondary-action edit-address-button"
                                            data-address='@json($address)'
                                        >
                                            Edit
                                        </button>

                                        @if(!$address['is_default'])
                                            <form method="POST" action="{{ url('/buyer/account/addresses/' . $address['id'] . '/default') }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="secondary-action">
                                                    Set as Default
                                                </button>
                                            </form>
                                        @endif

                                        <form
                                            method="POST"
                                            action="{{ url('/buyer/account/addresses/' . $address['id']) }}"
                                            onsubmit="return confirm('Delete this address?');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="danger-action" {{ $address['is_default'] ? 'disabled' : '' }}>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="address-card px-6 py-16 text-center">
                            <div class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-[#f5f2ed] text-[#aaa39b]">
                                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.6">
                                    <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z"></path>
                                    <circle cx="12" cy="10" r="2"></circle>
                                </svg>
                            </div>
                            <h2 class="mt-5 text-lg font-black">No saved addresses yet</h2>
                            <p class="mt-2 text-sm text-[#938c84]">Add an address to make checkout faster.</p>
                        </div>
                    @endforelse

                    <section class="border border-[#d7e9e2] bg-[#f4fbf8] p-5 sm:p-6">
                        <div class="flex gap-4">
                            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#079b72] text-white">
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path d="M12 3 19 6v5c0 5-3 8-7 10-4-2-7-5-7-10V6l7-3Z"></path>
                                </svg>
                            </div>

                            <div>
                                <p class="text-sm font-black">Delivery Privacy</p>
                                <p class="mt-1 text-xs leading-5 text-[#6f7f79]">
                                    Your saved address should only be shared with sellers and couriers when needed to fulfill an order.
                                </p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>
</main>

<div id="addressModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/45 p-4" aria-hidden="true">
    <div class="max-h-[92vh] w-full max-w-3xl overflow-y-auto bg-white shadow-2xl">
        <div class="sticky top-0 z-10 flex items-center justify-between border-b border-[#e5dfd8] bg-white px-5 py-4 sm:px-6">
            <div>
                <p class="address-eyebrow">DELIVERY ADDRESS</p>
                <h2 id="addressModalTitle" class="mt-1 text-lg font-black">Add New Address</h2>
            </div>

            <button
                id="closeAddressModal"
                type="button"
                class="grid h-9 w-9 place-items-center border border-[#ddd6ce] text-lg text-[#706961] transition hover:border-[#d92d2f] hover:text-[#d92d2f]"
            >
                ×
            </button>
        </div>

        <form id="addressForm" method="POST" action="{{ url('/buyer/account/addresses') }}" class="p-5 sm:p-6">
            @csrf
            <input id="addressMethod" type="hidden" name="_method" value="POST">

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="label" class="form-label">Address Label *</label>
                    <select id="label" name="label" class="address-input" required>
                        <option value="Home">Home</option>
                        <option value="Work">Work</option>
                        <option value="School">School</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div>
                    <label for="recipient" class="form-label">Recipient Name *</label>
                    <input id="recipient" name="recipient" type="text" class="address-input" required>
                </div>

                <div>
                    <label for="phone" class="form-label">Contact Number *</label>
                    <input id="phone" name="phone" type="tel" class="address-input" required>
                </div>

                <div>
                    <label for="postal_code" class="form-label">Postal Code</label>
                    <input id="postal_code" name="postal_code" type="text" class="address-input">
                </div>

                <div>
                    <label for="province" class="form-label">Province *</label>
                    <select id="province" name="province" class="address-input" required>
                        <option value="">Select Province</option>
                        <option>Metro Manila</option>
                        <option>Cavite</option>
                        <option>Laguna</option>
                        <option>Batangas</option>
                        <option>Rizal</option>
                        <option>Cebu</option>
                    </select>
                </div>

                <div>
                    <label for="municipality" class="form-label">Municipality / City *</label>
                    <select id="municipality" name="municipality" class="address-input" required>
                        <option value="">Select Municipality / City</option>
                        <option>Makati City</option>
                        <option>Quezon City</option>
                        <option>Manila</option>
                        <option>Pasig City</option>
                        <option>Cebu City</option>
                    </select>
                </div>

                <div>
                    <label for="barangay" class="form-label">Barangay *</label>
                    <select id="barangay" name="barangay" class="address-input" required>
                        <option value="">Select Barangay</option>
                        <option>San Antonio</option>
                        <option>Bel-Air</option>
                        <option>Poblacion</option>
                        <option>Guadalupe Nuevo</option>
                    </select>
                </div>

                <div>
                    <label for="house_number" class="form-label">House No. / Unit *</label>
                    <input id="house_number" name="house_number" type="text" class="address-input" required>
                </div>

                <div class="sm:col-span-2">
                    <label for="street" class="form-label">Street / Subdivision *</label>
                    <input id="street" name="street" type="text" class="address-input" required>
                </div>

                <div class="sm:col-span-2">
                    <label for="details" class="form-label">Additional Address Details</label>
                    <textarea
                        id="details"
                        name="details"
                        rows="3"
                        class="address-textarea"
                        placeholder="Landmark, building name, floor, delivery instructions..."
                    ></textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="flex cursor-pointer items-start gap-3 text-xs leading-5 text-[#716a63]">
                        <input id="is_default" type="checkbox" name="is_default" value="1" class="mt-0.5 h-4 w-4 accent-[#d92d2f]">
                        <span>Set this address as my default delivery address.</span>
                    </label>
                </div>
            </div>

            <div class="mt-7 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <button id="cancelAddressModal" type="button" class="secondary-action justify-center">
                    Cancel
                </button>

                <button type="submit" class="primary-action">
                    Save Address
                </button>
            </div>
        </form>
    </div>
</div>

<footer class="mt-4 bg-[#0a0a0a] text-white">
    <div class="likhae-container py-12">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-[1.5fr_repeat(4,1fr)]">
            <div>
                <a href="{{ url('/buyer/home') }}" class="flex items-center gap-2">
                    <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black">L</span>
                    <span class="text-xl font-black">LIKHAE</span>
                </a>

                <p class="mt-4 max-w-[230px] text-sm leading-6 text-white/35">
                    Shop More. Discover More. Live More. — Your Philippine marketplace.
                </p>
            </div>

            <div>
                <h3 class="footer-title">SHOP</h3>
                <div class="footer-links">
                    <a href="{{ url('/buyer/products') }}">All Products</a>
                    <a href="{{ url('/buyer/flash-deals') }}">Flash Deals</a>
                    <a href="{{ url('/buyer/local-finds') }}">Local Finds</a>
                </div>
            </div>

            <div>
                <h3 class="footer-title">MY ACCOUNT</h3>
                <div class="footer-links">
                    <a href="{{ url('/buyer/orders') }}">My Orders</a>
                    <a href="{{ url('/buyer/wishlist') }}">Wishlist</a>
                    <a href="{{ url('/buyer/messages') }}">Messages</a>
                </div>
            </div>

            <div>
                <h3 class="footer-title">SUPPORT</h3>
                <div class="footer-links">
                    <a href="#">Help Center</a>
                    <a href="{{ url('/buyer/orders') }}">Track Order</a>
                    <a href="#">Buyer Protection</a>
                </div>
            </div>

            <div>
                <h3 class="footer-title">COMPANY</h3>
                <div class="footer-links">
                    <a href="#">About LIKHAE</a>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms</a>
                </div>
            </div>
        </div>

        <div class="mt-12 border-t border-white/10 pt-6 text-[10px] text-white/25">
            © {{ date('Y') }} LIKHAE, Inc. — Made with pride in the Philippines.
        </div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('addressModal');
        const form = document.getElementById('addressForm');
        const title = document.getElementById('addressModalTitle');
        const methodInput = document.getElementById('addressMethod');

        const addButton = document.getElementById('addAddressButton');
        const closeButton = document.getElementById('closeAddressModal');
        const cancelButton = document.getElementById('cancelAddressModal');

        function openModal() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
        }

        function resetForCreate() {
            form.reset();
            form.action = "{{ url('/buyer/account/addresses') }}";
            methodInput.value = 'POST';
            title.textContent = 'Add New Address';
        }

        addButton?.addEventListener('click', function () {
            resetForCreate();
            openModal();
        });

        document.querySelectorAll('.edit-address-button').forEach(function (button) {
            button.addEventListener('click', function () {
                const address = JSON.parse(button.dataset.address);

                form.action = "{{ url('/buyer/account/addresses') }}/" + address.id;
                methodInput.value = 'PUT';
                title.textContent = 'Edit Address';

                document.getElementById('label').value = address.label || 'Home';
                document.getElementById('recipient').value = address.recipient || '';
                document.getElementById('phone').value = address.phone || '';
                document.getElementById('postal_code').value = address.postal_code || '';
                document.getElementById('province').value = address.province || '';
                document.getElementById('municipality').value = address.municipality || '';
                document.getElementById('barangay').value = address.barangay || '';
                document.getElementById('house_number').value = address.house_number || '';
                document.getElementById('street').value = address.street || '';
                document.getElementById('details').value = address.details || '';
                document.getElementById('is_default').checked = !!address.is_default;

                openModal();
            });
        });

        closeButton?.addEventListener('click', closeModal);
        cancelButton?.addEventListener('click', closeModal);

        modal?.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });

        /*
         * Address API integration placeholder:
         * province -> municipality/city -> barangay
         *
         * Later, replace the static select options with your Philippine
         * Standard Geographic Code (PSGC) API response.
         */
    });
</script>

</body>
</html>