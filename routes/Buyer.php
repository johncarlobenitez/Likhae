<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

$buyerProducts = [
    ['id'=>'likhae-test-handcrafted-shirt','slug'=>'likhae-test-handcrafted-shirt','name'=>'LIKHAE Test Handcrafted Shirt','category'=>"Women's Apparel",'subcategory'=>'Tops & Blouses','seller'=>'LIKHAE Studio','seller_slug'=>'likhae-studio','location'=>'Santa Cruz, Laguna','price'=>1080,'old_price'=>1200,'discount'=>10,'rating'=>4.8,'reviews'=>0,'sold'=>0,'stock'=>28,'is_demo'=>true,'image'=>'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=900&q=80','gallery'=>['https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=900&q=80','https://images.unsplash.com/photo-1503341504253-dff4815485f1?w=900&q=80'],'description'=>'Locally crafted test apparel used to validate the LIKHAE marketplace order and delivery workflow.','specs'=>['Material'=>'Cotton Blend','Origin'=>'Local','Care'=>'Hand wash / gentle wash'],'variations'=>['Color'=>['Black','White'],'Size'=>['Small','Medium','Large']],'variant_stock'=>['Black / Small'=>10,'Black / Medium'=>5,'Black / Large'=>0,'White / Small'=>8,'White / Medium'=>3,'White / Large'=>2]],
    ['id'=>'wireless-headphones','slug'=>'wireless-headphones','name'=>'Premium Wireless Headphones','category'=>'Electronics','seller'=>'Metro Finds PH','location'=>'Makati City','price'=>2499,'old_price'=>2999,'discount'=>17,'rating'=>4.8,'reviews'=>128,'sold'=>250,'stock'=>18,'image'=>'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&q=80'],
    ['id'=>'classic-backpack','slug'=>'classic-backpack','name'=>'Classic Everyday Backpack','category'=>'Bags','seller'=>'Urban Carry Co.','location'=>'Quezon City','price'=>999,'old_price'=>1299,'discount'=>23,'rating'=>4.7,'reviews'=>92,'sold'=>410,'stock'=>26,'image'=>'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400&q=80'],
    ['id'=>'running-shoes','slug'=>'running-shoes','name'=>'Lightweight Running Shoes','category'=>'Sports & Outdoors','seller'=>'Stride PH','location'=>'Pasig City','price'=>1799,'old_price'=>2199,'discount'=>18,'rating'=>4.9,'reviews'=>205,'sold'=>540,'stock'=>14,'image'=>'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80'],
    ['id'=>'smart-watch','slug'=>'smart-watch','name'=>'Everyday Smart Watch','category'=>'Electronics','seller'=>'Tech Avenue','location'=>'Taguig City','price'=>2190,'old_price'=>2690,'discount'=>19,'rating'=>4.7,'reviews'=>164,'sold'=>325,'stock'=>20,'image'=>'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&q=80'],
    ['id'=>'skincare-set','slug'=>'skincare-set','name'=>'Daily Skincare Essentials Set','category'=>'Beauty & Health','seller'=>'Glow Market','location'=>'Manila','price'=>899,'old_price'=>1099,'discount'=>18,'rating'=>4.8,'reviews'=>110,'sold'=>290,'stock'=>31,'image'=>'https://images.unsplash.com/photo-1556228578-8c89e6adf883?w=400&q=80'],
    ['id'=>'mechanical-keyboard','slug'=>'mechanical-keyboard','name'=>'Compact Mechanical Keyboard','category'=>'Electronics','seller'=>'KeyHub PH','location'=>'Mandaluyong City','price'=>1899,'old_price'=>2299,'discount'=>17,'rating'=>4.9,'reviews'=>180,'sold'=>460,'stock'=>17,'image'=>'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=400&q=80'],
    ['id'=>'coffee-maker','slug'=>'coffee-maker','name'=>'Compact Home Coffee Maker','category'=>'Home & Living','seller'=>'Kitchen+ Manila','location'=>'Manila','price'=>1599,'old_price'=>1899,'discount'=>16,'rating'=>4.6,'reviews'=>84,'sold'=>170,'stock'=>11,'image'=>'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400&q=80'],
    ['id'=>'desk-lamp','slug'=>'desk-lamp','name'=>'Minimal Adjustable Desk Lamp','category'=>'Home & Living','seller'=>'Home Basics MNL','location'=>'Marikina City','price'=>749,'old_price'=>899,'discount'=>17,'rating'=>4.6,'reviews'=>71,'sold'=>180,'stock'=>22,'image'=>'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=400&q=80'],
    ['id'=>'basketball','slug'=>'basketball','name'=>'Indoor / Outdoor Basketball','category'=>'Sports & Outdoors','seller'=>'Playground PH','location'=>'Cavite','price'=>699,'old_price'=>849,'discount'=>18,'rating'=>4.7,'reviews'=>89,'sold'=>201,'stock'=>34,'image'=>'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=400&q=80'],
    ['id'=>'book-set','slug'=>'book-set','name'=>'Modern Reading Essentials Set','category'=>'Books & Stationery','seller'=>'Paper Trail PH','location'=>'Laguna','price'=>559,'old_price'=>699,'discount'=>20,'rating'=>4.8,'reviews'=>74,'sold'=>150,'stock'=>42,'image'=>'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=400&q=80'],
    ['id'=>'pet-supplies','slug'=>'pet-supplies','name'=>'Everyday Pet Care Starter Set','category'=>'Pet Supplies','seller'=>'Happy Paws PH','location'=>'Quezon City','price'=>799,'old_price'=>949,'discount'=>16,'rating'=>4.9,'reviews'=>98,'sold'=>220,'stock'=>28,'image'=>'https://images.unsplash.com/photo-1601758124510-52d02ddb7cbd?w=400&q=80'],
];

View::share('buyerProducts', collect($buyerProducts));

$buyerSampleOrder = [
    'id' => 'ORD-TEST-0001',
    'tracking' => 'LH-TEST-0001',
    'placed_at' => 'September 7, 2026, 9:14 AM',
    'payment' => 'Cash on Delivery',
    'status' => 'to-receive',
    'status_label' => 'Delivered - Confirm Receipt',
    'delivered_at' => 'September 7, 2026, 2:35 PM',
    'auto_receive_at' => 'September 10, 2026',
    'total' => 2280,
    'products' => [[
        'name' => 'LIKHAE Test Handcrafted Shirt',
        'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=900&q=80',
        'variant' => 'Black / Medium',
        'quantity' => 2,
        'price' => 1080,
    ]],
    'timeline' => [
        ['label' => 'Order Placed', 'time' => 'September 7, 2026, 9:14 AM', 'done' => true],
        ['label' => 'Seller Confirmed', 'time' => 'September 7, 2026, 9:30 AM', 'done' => true],
        ['label' => 'Preparing Order', 'time' => 'September 7, 2026, 10:05 AM', 'done' => true],
        ['label' => 'Ready for Pickup', 'time' => 'September 7, 2026, 11:20 AM', 'done' => true],
        ['label' => 'Picked Up', 'time' => 'September 7, 2026, 12:10 PM', 'done' => true],
        ['label' => 'At Sorting Center', 'time' => 'September 7, 2026, 12:45 PM', 'done' => true],
        ['label' => 'Assigned to Rider', 'time' => 'September 7, 2026, 1:20 PM', 'done' => true],
        ['label' => 'Out for Delivery', 'time' => 'September 7, 2026, 1:50 PM', 'done' => true],
        ['label' => 'Delivered', 'time' => 'September 7, 2026, 2:35 PM', 'done' => true],
        ['label' => 'Completed', 'time' => 'Waiting for buyer confirmation', 'done' => false],
    ],
];

View::composer('Buyer.orders', function ($view) use ($buyerSampleOrder) {
    $order = $buyerSampleOrder;

    if (session('buyer_sample_order_received')) {
        $order['status'] = 'completed';
        $order['status_label'] = 'Completed';
        $order['timeline'][9] = ['label' => 'Completed', 'time' => 'Confirmed by buyer', 'done' => true];
    }

    $view->with('buyerOrders', collect([$order]));
});

Route::prefix('buyer')->name('buyer.')->middleware(['auth', \App\Http\Middleware\EnsureWorkspaceRole::class.':buyer'])->group(function () {
    Route::get('/pending', fn () => redirect()->route('buyer.home'))->name('pending');

    Route::get('/home', fn () => view('Buyer.home'))->name('home');

    Route::get('/products', fn () => view('Buyer.products', ['mode' => 'grid']))->name('products');
    Route::get('/products/{slug}', function (string $slug) {
        $product = collect(view()->shared('buyerProducts', []))->firstWhere('slug', $slug);
        abort_if(!$product, 404);
        return view('Buyer.product-details', [
            'product' => $product,
        ]);
    })->name('product-details');

    Route::get('/flash-deals', fn () => view('Buyer.products', [
        'mode' => 'grid',
        'focus' => 'deals',
    ]))->name('flash-deals');
    Route::get('/local-finds', fn () => view('Buyer.products', [
        'mode' => 'grid',
        'focus' => 'local',
    ]))->name('local-finds');

    Route::get('/cart', function () {
        $addSlug = request('add');
        if ($addSlug) {
            $product = collect(view()->shared('buyerProducts', []))->firstWhere('slug', $addSlug);
            if ($product) {
                $color = (string) request('color', '');
                $size = (string) request('size', '');
                $variant = trim(implode(' / ', array_filter([$color, $size])));
                $variantStock = (int) data_get($product, 'variant_stock.'.$variant, data_get($product, 'stock', 0));
                $quantity = max(1, min((int) request('quantity', 1), $variantStock));
                if ($variantStock < 1) return redirect()->route('buyer.product-details', ['slug' => $addSlug])->with('buyer_notice', 'That variation is out of stock.');
                $cart = session('cart', []);
                $existing = collect($cart)->search(fn($i) => ($i['slug'] ?? null) === $addSlug && ($i['variant'] ?? '') === $variant);
                if ($existing !== false) {
                    $cart[$existing]['quantity'] = min(($cart[$existing]['quantity'] ?? 1) + $quantity, $variantStock);
                } else {
                    $cart[] = array_merge($product, ['quantity' => $quantity, 'variant' => $variant ?: 'Standard', 'stock' => $variantStock]);
                }
                session(['cart' => $cart]);
                if (request()->boolean('checkout')) return redirect()->route('buyer.checkout');
            }
        }
        return view('Buyer.cart');
    })->name('cart');
    Route::get('/checkout', fn () => view('Buyer.checkout'))->name('checkout');
    Route::post('/checkout', fn () => view('Buyer.checkout'))->name('checkout.post');
    Route::post('/order', fn () => redirect()->route('buyer.orders.success'))->name('order.store');
    Route::post('/orders/cancel', fn () => redirect()->route('buyer.orders')->with('status', 'Order cancelled.'))->name('orders.cancel');
    Route::post('/orders/{id}/received', function (string $id) {
        abort_unless($id === 'ORD-TEST-0001', 404);
        session(['buyer_sample_order_received' => true]);

        return redirect()->route('buyer.orders.show', ['id' => $id])
            ->with('buyer_notice', 'Order received. The order is now completed and ready for your review.');
    })->name('orders.received');
    Route::get('/orders/{id}/return', fn (string $id) => view('Buyer.orders', ['mode' => 'return', 'selectedOrderId' => $id]))->name('orders.return');
    Route::post('/orders/{id}/return', fn (string $id) => redirect()->route('buyer.orders.show', ['id' => $id])->with('buyer_notice', 'Return or refund request submitted for seller review.'))->name('orders.return.store');
    Route::post('/orders/{id}/review', fn (string $id) => redirect()->route('buyer.orders.show', ['id' => $id])->with('buyer_notice', 'Review submitted.'))->name('orders.review.store');

    Route::get('/orders/success', fn () => view('Buyer.orders', ['mode' => 'success']))->name('orders.success');
    Route::get('/orders', fn () => view('Buyer.orders', ['mode' => 'index']))->name('orders');
    Route::get('/orders/{id}', fn (string $id) => view('Buyer.orders', [
        'mode' => 'show',
        'selectedOrderId' => $id,
    ]))->name('orders.show');
    Route::get('/orders/{id}/review', fn (string $id) => view('Buyer.orders', [
        'mode' => 'review',
        'selectedOrderId' => $id,
    ]))->name('orders.review');

    Route::get('/messages', fn () => view('Buyer.messages'))->name('messages');
    Route::get('/wishlist', fn () => view('Buyer.wishlist'))->name('wishlist');
    Route::get('/notifications', fn () => view('Buyer.notifications'))->name('notifications');

    Route::get('/shop/{seller}', function (string $seller) {
        $products = collect(view()->shared('buyerProducts', []));
        $matchedProduct = $products->first(function ($p) use ($seller) {
            $slug = data_get($p, 'seller_slug', \Illuminate\Support\Str::slug((string) data_get($p, 'seller', '')));
            return $slug === $seller;
        });

        $sellerName = $matchedProduct ? data_get($matchedProduct, 'seller') : ucwords(str_replace('-', ' ', $seller));
        $sellerLocation = $matchedProduct ? data_get($matchedProduct, 'location') : 'Metro Manila, Philippines';

        $sellerData = [
            'name' => $sellerName,
            'slug' => $seller,
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($sellerName) . '&background=7f1d1d&color=fff',
            'location' => $sellerLocation,
            'joined' => '2023',
            'rating' => '4.8',
            'followers' => '1.2k',
            'response' => '98%',
            'fulfillment' => '99%',
            'description' => 'Official LIKHAE Verified Seller providing authentic, quality local products with fast order processing and customer satisfaction.',
            'hours' => '8:00 AM - 6:00 PM',
        ];

        return view('Buyer.store', [
            'seller' => $sellerData,
            'sellerSlug' => $seller,
        ]);
    })->name('shop');

    Route::get('/account', fn () => view('Buyer.account', [
        'tab' => request('tab', 'profile'),
    ]))->name('account');
    Route::get('/account/profile', fn () => view('Buyer.account', ['tab' => 'profile']))->name('account.profile');
    Route::get('/account/addresses', fn () => view('Buyer.account', ['tab' => 'addresses']))->name('account.addresses');
    Route::get('/account/security', fn () => view('Buyer.account', ['tab' => 'security']))->name('account.security');
    Route::get('/account/reviews', fn () => view('Buyer.account', ['tab' => 'reviews']))->name('account.reviews');

    Route::get('/rewards', fn () => view('Buyer.rewards'))->name('rewards');
});
