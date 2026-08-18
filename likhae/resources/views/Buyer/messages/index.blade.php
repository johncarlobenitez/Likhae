<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Messages — LIKHAE</title>

    @vite([
        'resources/css/buyer/messages.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-[#f5f2ed] text-[#111111] antialiased">

@php
    /*
    |--------------------------------------------------------------------------
    | Demo Buyer / Messaging Data
    |--------------------------------------------------------------------------
    | Replace with real conversation/message models later.
    */
    $buyer = [
        'first_name' => auth()->check() ? explode(' ', auth()->user()->name)[0] : 'Juan',
        'cart_count' => 2,
        'message_count' => 3,
        'notification_count' => 4,
    ];

    $conversations = [
        [
            'id' => 1,
            'type' => 'seller',
            'name' => 'TECHHUB PH',
            'subtitle' => 'Seller',
            'initials' => 'TH',
            'last_message' => 'Yes, the black color is still available.',
            'time' => '11:42 AM',
            'unread' => 2,
            'online' => true,
            'order_number' => 'LH-20260818-0001',
        ],
        [
            'id' => 2,
            'type' => 'courier',
            'name' => 'Miguel R. Santos',
            'subtitle' => 'Courier',
            'initials' => 'MR',
            'last_message' => 'Your parcel is currently in transit.',
            'time' => '9:18 AM',
            'unread' => 1,
            'online' => true,
            'order_number' => 'LH-20260816-0008',
        ],
        [
            'id' => 3,
            'type' => 'seller',
            'name' => 'STYLE MANILA',
            'subtitle' => 'Seller',
            'initials' => 'SM',
            'last_message' => 'Thank you for your order!',
            'time' => 'Yesterday',
            'unread' => 0,
            'online' => false,
            'order_number' => 'LH-20260816-0008',
        ],
        [
            'id' => 4,
            'type' => 'support',
            'name' => 'LIKHAE Support',
            'subtitle' => 'Support',
            'initials' => 'LS',
            'last_message' => 'How can we help you today?',
            'time' => 'Aug 16',
            'unread' => 0,
            'online' => true,
            'order_number' => null,
        ],
    ];

    $activeConversation = $conversations[0];

    $messages = [
        [
            'sender' => 'them',
            'text' => 'Hi Juan! Thank you for your order. We are preparing your Baseus Wireless Earbuds now.',
            'time' => '10:15 AM',
        ],
        [
            'sender' => 'me',
            'text' => 'Hi! Is the black color still available?',
            'time' => '10:18 AM',
        ],
        [
            'sender' => 'them',
            'text' => 'Yes, the black color is still available. We will ship the black variant selected in your order.',
            'time' => '10:20 AM',
        ],
        [
            'sender' => 'me',
            'text' => 'Great, thank you. Please make sure the item is sealed.',
            'time' => '10:25 AM',
        ],
        [
            'sender' => 'them',
            'text' => 'Of course. We will inspect the package before handing it to the courier.',
            'time' => '11:42 AM',
        ],
    ];
@endphp

{{-- =========================================================
     HEADER
========================================================= --}}
<header class="sticky top-0 z-50 border-b border-black/10 bg-white/95 backdrop-blur">
    <div class="likhae-container">
        <div class="flex h-16 items-center gap-4">
            <a href="{{ url('/buyer/home') }}" class="flex shrink-0 items-center gap-2">
                <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black text-white">
                    L
                </span>
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

                <a href="{{ url('/buyer/messages') }}" class="buyer-header-action text-[#d92d2f]">
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
    {{-- =====================================================
         PAGE HEADER
    ====================================================== --}}
    <section class="border-b border-[#ded8d0] bg-white">
        <div class="likhae-container py-8">
            <div class="text-[11px] text-[#9b958d]">
                <a href="{{ url('/buyer/home') }}" class="transition hover:text-[#d92d2f]">Home</a>
                <span class="mx-2">/</span>
                <span class="text-[#4d4944]">Messages</span>
            </div>

            <div class="mt-5">
                <p class="buyer-section-eyebrow">CONVERSATIONS</p>

                <h1 class="mt-3 text-3xl font-black tracking-[-0.04em] sm:text-4xl">
                    Messages
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-6 text-[#8b847c]">
                    Chat with sellers, couriers, and LIKHAE support about your orders and marketplace concerns.
                </p>
            </div>
        </div>
    </section>

    {{-- =====================================================
         MESSAGING LAYOUT
    ====================================================== --}}
    <section class="py-8 lg:py-10">
        <div class="likhae-container">
            <div class="messages-shell">

                {{-- =========================================
                     CONVERSATIONS SIDEBAR
                ========================================== --}}
                <aside class="conversation-panel" id="conversationPanel">
                    <div class="border-b border-[#e7e1da] p-4">
                        <div class="relative">
                            <svg
                                viewBox="0 0 24 24"
                                class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#aaa39b]"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                            >
                                <circle cx="11" cy="11" r="7"></circle>
                                <path d="m20 20-3.5-3.5"></path>
                            </svg>

                            <input
                                id="conversationSearch"
                                type="search"
                                placeholder="Search conversations..."
                                class="h-10 w-full border border-[#ddd6ce] bg-[#faf8f5] pl-10 pr-3 text-xs outline-none transition focus:border-[#d92d2f]"
                            >
                        </div>
                    </div>

                    <div class="flex border-b border-[#e7e1da]">
                        <button type="button" class="conversation-filter is-active" data-filter="all">All</button>
                        <button type="button" class="conversation-filter" data-filter="seller">Sellers</button>
                        <button type="button" class="conversation-filter" data-filter="courier">Couriers</button>
                    </div>

                    <div id="conversationList" class="conversation-list">
                        @foreach($conversations as $conversation)
                            <button
                                type="button"
                                class="conversation-item {{ $conversation['id'] === $activeConversation['id'] ? 'is-active' : '' }}"
                                data-type="{{ $conversation['type'] }}"
                                data-name="{{ strtolower($conversation['name']) }}"
                            >
                                <span class="relative shrink-0">
                                    <span class="conversation-avatar">
                                        {{ $conversation['initials'] }}
                                    </span>

                                    @if($conversation['online'])
                                        <span class="online-dot"></span>
                                    @endif
                                </span>

                                <span class="min-w-0 flex-1 text-left">
                                    <span class="flex items-start justify-between gap-2">
                                        <span class="truncate text-xs font-black">
                                            {{ $conversation['name'] }}
                                        </span>

                                        <span class="shrink-0 text-[9px] text-[#aaa39b]">
                                            {{ $conversation['time'] }}
                                        </span>
                                    </span>

                                    <span class="mt-1 flex items-center gap-2">
                                        <span class="conversation-type">
                                            {{ $conversation['subtitle'] }}
                                        </span>

                                        @if($conversation['order_number'])
                                            <span class="truncate text-[9px] text-[#aaa39b]">
                                                · {{ $conversation['order_number'] }}
                                            </span>
                                        @endif
                                    </span>

                                    <span class="mt-2 flex items-center justify-between gap-2">
                                        <span class="truncate text-[11px] text-[#817a72]">
                                            {{ $conversation['last_message'] }}
                                        </span>

                                        @if($conversation['unread'] > 0)
                                            <span class="unread-badge">
                                                {{ $conversation['unread'] }}
                                            </span>
                                        @endif
                                    </span>
                                </span>
                            </button>
                        @endforeach
                    </div>
                </aside>

                {{-- =========================================
                     CHAT AREA
                ========================================== --}}
                <section class="chat-panel">

                    {{-- Chat Header --}}
                    <div class="chat-header">
                        <button
                            id="mobileBackButton"
                            type="button"
                            class="grid h-9 w-9 shrink-0 place-items-center border border-[#ddd6ce] text-[#716a63] md:hidden"
                            aria-label="Back to conversations"
                        >
                            ←
                        </button>

                        <span class="relative shrink-0">
                            <span class="conversation-avatar bg-[#111] text-white">
                                {{ $activeConversation['initials'] }}
                            </span>
                            <span class="online-dot"></span>
                        </span>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="truncate text-sm font-black">
                                    {{ $activeConversation['name'] }}
                                </h2>

                                <span class="conversation-type">
                                    {{ $activeConversation['subtitle'] }}
                                </span>
                            </div>

                            <p class="mt-1 text-[10px] text-[#079b72]">
                                Online now
                            </p>
                        </div>

                        <div class="flex shrink-0 gap-2">
                            @if($activeConversation['order_number'])
                                <a
                                    href="{{ url('/buyer/orders/' . $activeConversation['order_number']) }}"
                                    class="chat-header-action"
                                >
                                    View Order
                                </a>
                            @endif

                            <button
                                type="button"
                                class="chat-icon-button"
                                aria-label="Conversation options"
                            >
                                ⋯
                            </button>
                        </div>
                    </div>

                    {{-- Order Context --}}
                    @if($activeConversation['order_number'])
                        <div class="order-context">
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-[0.15em] text-[#918a82]">
                                    RELATED ORDER
                                </p>

                                <p class="mt-1 text-xs font-bold">
                                    {{ $activeConversation['order_number'] }}
                                </p>
                            </div>

                            <div class="text-right">
                                <p class="text-[9px] uppercase tracking-[0.12em] text-[#9e978f]">
                                    Status
                                </p>

                                <p class="mt-1 text-[10px] font-black text-[#d92d2f]">
                                    TO SHIP
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- Messages --}}
                    <div id="messagesArea" class="messages-area">
                        <div class="message-date-divider">
                            <span>Today</span>
                        </div>

                        @foreach($messages as $message)
                            <div class="message-row {{ $message['sender'] === 'me' ? 'is-me' : 'is-them' }}">
                                <div class="message-bubble">
                                    <p>{{ $message['text'] }}</p>

                                    <div class="mt-2 flex items-center justify-end gap-2">
                                        <span class="text-[9px] opacity-60">
                                            {{ $message['time'] }}
                                        </span>

                                        @if($message['sender'] === 'me')
                                            <span class="text-[9px] opacity-60">
                                                ✓✓
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Composer --}}
                    <form
                        id="messageForm"
                        method="POST"
                        action="{{ url('/buyer/messages/' . $activeConversation['id']) }}"
                        enctype="multipart/form-data"
                        class="message-composer"
                    >
                        @csrf

                        <label
                            for="attachmentInput"
                            class="composer-icon-button"
                            aria-label="Attach file"
                        >
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="m21 11.5-8.6 8.6a6 6 0 0 1-8.5-8.5l9.2-9.2a4 4 0 0 1 5.7 5.7L9.6 17.3a2 2 0 0 1-2.8-2.8L15 6.3"></path>
                            </svg>
                        </label>

                        <input
                            id="attachmentInput"
                            name="attachment"
                            type="file"
                            accept="image/jpeg,image/png,application/pdf"
                            class="sr-only"
                        >

                        <textarea
                            id="messageInput"
                            name="message"
                            rows="1"
                            maxlength="2000"
                            placeholder="Type a message..."
                            class="message-input"
                        ></textarea>

                        <button
                            id="sendButton"
                            type="submit"
                            class="send-button"
                        >
                            <span class="hidden sm:inline">Send</span>
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="m22 2-7 20-4-9-9-4 20-7Z"></path>
                                <path d="M22 2 11 13"></path>
                            </svg>
                        </button>
                    </form>

                    <div id="attachmentPreview" class="hidden border-t border-[#e7e1da] bg-[#faf8f5] px-4 py-2 text-[10px] text-[#716a63]"></div>
                </section>
            </div>
        </div>
    </section>
</main>

{{-- =========================================================
     FOOTER
========================================================= --}}
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
                    <a href="{{ url('/buyer/account') }}">Profile</a>
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
        const searchInput = document.getElementById('conversationSearch');
        const conversationItems = Array.from(
            document.querySelectorAll('.conversation-item')
        );
        const filterButtons = Array.from(
            document.querySelectorAll('.conversation-filter')
        );

        let activeFilter = 'all';

        function filterConversations() {
            const searchTerm = searchInput.value.trim().toLowerCase();

            conversationItems.forEach(function (item) {
                const matchesType =
                    activeFilter === 'all' ||
                    item.dataset.type === activeFilter;

                const matchesSearch =
                    item.dataset.name.includes(searchTerm);

                item.classList.toggle(
                    'hidden',
                    !(matchesType && matchesSearch)
                );
            });
        }

        searchInput.addEventListener('input', filterConversations);

        filterButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                activeFilter = button.dataset.filter;

                filterButtons.forEach(function (item) {
                    item.classList.remove('is-active');
                });

                button.classList.add('is-active');
                filterConversations();
            });
        });

        const messagesArea = document.getElementById('messagesArea');

        if (messagesArea) {
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }

        const messageInput = document.getElementById('messageInput');

        if (messageInput) {
            function resizeTextarea() {
                messageInput.style.height = 'auto';
                messageInput.style.height =
                    Math.min(messageInput.scrollHeight, 120) + 'px';
            }

            messageInput.addEventListener('input', resizeTextarea);
            resizeTextarea();
        }

        const attachmentInput = document.getElementById('attachmentInput');
        const attachmentPreview = document.getElementById('attachmentPreview');

        if (attachmentInput && attachmentPreview) {
            attachmentInput.addEventListener('change', function () {
                if (!attachmentInput.files.length) {
                    attachmentPreview.classList.add('hidden');
                    attachmentPreview.textContent = '';
                    return;
                }

                attachmentPreview.classList.remove('hidden');
                attachmentPreview.textContent =
                    'Attachment: ' + attachmentInput.files[0].name;
            });
        }

        const shell = document.querySelector('.messages-shell');
        const conversationPanel = document.getElementById('conversationPanel');
        const mobileBackButton = document.getElementById('mobileBackButton');

        conversationItems.forEach(function (item) {
            item.addEventListener('click', function () {
                conversationItems.forEach(function (conversation) {
                    conversation.classList.remove('is-active');
                });

                item.classList.add('is-active');

                if (window.innerWidth < 768 && shell) {
                    shell.classList.add('show-chat');
                }
            });
        });

        if (mobileBackButton && shell) {
            mobileBackButton.addEventListener('click', function () {
                shell.classList.remove('show-chat');
            });
        }
    });
</script>

</body>
</html>