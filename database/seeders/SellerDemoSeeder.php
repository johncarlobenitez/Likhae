<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Delivery;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\SellerCampaign;
use App\Models\SellerProfile;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WorkspaceNotification;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SellerDemoSeeder extends Seeder
{
    public function run(): void
    {
        $seller = User::updateOrCreate(
            ['email' => 'seller@likhae.com'],
            [
                'name' => 'Mariel Santos',
                'first_name' => 'Mariel',
                'last_name' => 'Santos',
                'role' => 'seller',
                'status' => 'active',
                'password' => Hash::make('Password1'),
                'contact_number' => '09170002481',
                'business_name' => 'LIKHAE Studio Trading',
                'store_name' => 'LIKHAE Studio',
                'business_type' => 'Sole Proprietorship',
                'dti_sec_number' => 'DTI-2026-104829',
                'tin' => '123-456-482-000',
                'province' => 'Laguna',
                'municipality' => 'Santa Cruz',
                'barangay' => 'Poblacion',
                'street' => 'Rizal Street',
                'house_number' => '24',
            ]
        );

        $buyers = collect([
            ['Angela Cruz', 'angela@likhae.test'],
            ['Marco Reyes', 'marco@likhae.test'],
            ['Sarah Lim', 'sarah@likhae.test'],
            ['Daniel Tan', 'daniel@likhae.test'],
            ['Patricia Go', 'patricia@likhae.test'],
            ['John Villanueva', 'john@likhae.test'],
        ])->map(fn ($data) => User::updateOrCreate(
            ['email' => $data[1]],
            [
                'name' => $data[0],
                'role' => 'buyer',
                'status' => 'active',
                'password' => Hash::make('Password1'),
                'province' => 'Laguna',
                'municipality' => 'Calamba City',
            ]
        ));

        $rider = User::updateOrCreate(
            ['email' => 'rider@likhae.com'],
            [
                'name' => 'Juan Rider',
                'role' => 'rider',
                'status' => 'active',
                'password' => Hash::make('Password1'),
            ]
        );

        $categories = collect([
            'Electronics', 'Audio', 'Home Office', 'Accessories', "Women's Apparel",
        ])->mapWithKeys(function ($name) {
            $category = Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'status' => 'active']
            );
            return [$name => $category];
        });

        $productRows = [
            ['LIKHAE Test Handcrafted Shirt', "Women's Apparel", 'LK-TEST-SHIRT', 1080, 28, 'active', 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=600&q=85'],
            ['27-inch Borderless Monitor', 'Electronics', 'MON-27-BLK', 12990, 20, 'active', 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=600&q=85'],
            ['Mechanical Keyboard 87 Keys', 'Electronics', 'KEY-MECH-87', 2790, 3, 'active', 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=600&q=85'],
            ['Wireless Gaming Mouse', 'Electronics', 'MSE-GM-BLK', 1490, 5, 'active', 'https://images.unsplash.com/photo-1527814050087-3793815479db?w=600&q=85'],
            ['Studio Wireless Headphones', 'Audio', 'HDP-BT-NVY', 3490, 18, 'active', 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&q=85'],
            ['Adjustable LED Desk Lamp', 'Home Office', 'LMP-DSK-WHT', 990, 42, 'active', 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=600&q=85'],
            ['7-in-1 USB-C Hub', 'Accessories', 'HUB-USBC-07', 1890, 0, 'archived', 'https://images.unsplash.com/photo-1625842268584-8f3296236761?w=600&q=85'],
        ];

        $products = collect($productRows)->map(function ($row) use ($seller, $categories) {
            return Product::updateOrCreate(
                ['seller_id' => $seller->id, 'sku' => $row[2]],
                [
                    'category_id' => $categories[$row[1]]->id,
                    'name' => $row[0],
                    'slug' => Str::slug($row[0]).'-'.$seller->id,
                    'description' => 'Buyer-ready demo listing for '.$row[0].'. Update this content from Seller Center.',
                    'price' => $row[3],
                    'stock' => $row[4],
                    'status' => $row[5],
                    'image_path' => $row[6],
                ]
            );
        });

        $orderRows = [
            ['10001', 0, 1, 1, 'gcash', 'to_process', 'Calamba City, Laguna 4027'],
            ['10002', 1, 2, 2, 'cod', 'to_prepare', 'Los Baños, Laguna 4030'],
            ['10003', 2, 3, 1, 'maya', 'ready_pickup', 'Santa Rosa, Laguna 4026'],
            ['10004', 3, 4, 1, 'card', 'shipping', 'Biñan, Laguna 4024'],
            ['10005', 4, 5, 2, 'gcash', 'completed', 'Calamba City, Laguna 4027'],
            ['10006', 5, 6, 1, 'cod', 'returns', 'San Pablo City, Laguna 4000'],
        ];

        foreach ($orderRows as $index => $row) {
            $product = $products[$row[2]];
            $buyer = $buyers[$row[1]];
            $total = (float) $product->price * $row[3];
            $createdAt = now()->subDays(5 - min($index, 5))->setTime(9 + $index, 20);

            $order = Order::updateOrCreate(
                ['order_number' => $row[0]],
                [
                    'buyer_id' => $buyer->id,
                    'seller_id' => $seller->id,
                    'total_amount' => $total,
                    'payment_method' => $row[4],
                    'payment_status' => in_array($row[5], ['completed', 'shipping', 'ready_pickup'], true) ? 'paid' : 'pending',
                    'status' => $row[5],
                    'shipping_address' => $row[6],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]
            );

            OrderItem::updateOrCreate(
                ['order_id' => $order->id, 'product_id' => $product->id],
                [
                    'quantity' => $row[3],
                    'unit_price' => $product->price,
                    'subtotal' => $total,
                ]
            );

            if (in_array($row[5], ['completed', 'shipping'], true)) {
                Transaction::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'transaction_number' => 'TXN-2026-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT),
                        'buyer_id' => $buyer->id,
                        'amount' => $total,
                        'method' => $row[4],
                        'status' => $row[5] === 'completed' ? 'paid' : 'pending',
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]
                );
            }

            if (in_array($row[5], ['shipping', 'completed'], true)) {
                Delivery::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'rider_id' => $rider->id,
                        'address' => $row[6],
                        'status' => $row[5] === 'completed' ? 'delivered' : 'out_for_delivery',
                        'provider' => 'LIKHAE Logistics',
                        'tracking_number' => 'LH-2026-'.$row[0],
                        'pickup_window' => $createdAt->format('M d, Y').' · 10:00 AM – 12:00 PM',
                        'pickup_note' => 'Package is sealed and available at the store counter.',
                        'requested_at' => $createdAt->copy()->subDay(),
                        'assigned_at' => $createdAt->copy()->subHours(2),
                        'delivered_at' => $row[5] === 'completed' ? $createdAt->copy()->addDay() : null,
                    ]
                );
            }
        }

        $reviews = [
            [0, 1, 5, 'The monitor arrived safely and the colors look great. Seller packed it very well and shipped fast.', false],
            [1, 2, 5, 'Solid build and responsive keys. The seller also answered my questions before I ordered.', true],
            [2, 3, 4, 'Good mouse for the price. Delivery was quick, but I hope more color options become available.', false],
        ];

        foreach ($reviews as $index => $row) {
            ProductReview::updateOrCreate(
                ['product_id' => $products[$row[1]]->id, 'buyer_id' => $buyers[$row[0]]->id],
                [
                    'seller_id' => $seller->id,
                    'rating' => $row[2],
                    'body' => $row[3],
                    'reply' => $row[4] ? 'Thank you for your feedback! We are glad you are enjoying your order.' : null,
                    'replied_at' => $row[4] ? now()->subDay() : null,
                    'has_photo' => $index !== 2,
                    'created_at' => now()->subDays($index),
                    'updated_at' => now()->subDays($index),
                ]
            );
        }

        $messagePairs = [
            [0, 'Hello! Is the 27-inch monitor compatible with a MacBook using USB-C?', false],
            [1, 'Thank you, I received the waybill update.', true],
            [2, 'Can I change the delivery address?', false],
            [3, 'The headphones sound great!', true],
            [4, 'Do you have this lamp in black?', true],
        ];

        foreach ($messagePairs as $index => $row) {
            $buyer = $buyers[$row[0]];
            Message::firstOrCreate(
                [
                    'sender_id' => $buyer->id,
                    'recipient_id' => $seller->id,
                    'body' => $row[1],
                ],
                [
                    'order_id' => Order::where('seller_id', $seller->id)->where('buyer_id', $buyer->id)->value('id'),
                    'read_at' => $row[2] ? now()->subMinutes(5) : null,
                    'created_at' => now()->subMinutes(($index + 1) * 12),
                    'updated_at' => now()->subMinutes(($index + 1) * 12),
                ]
            );
        }

        SellerCampaign::updateOrCreate(
            ['seller_id' => $seller->id, 'name' => 'September Local Finds'],
            [
                'type' => 'discount',
                'discount_type' => 'percent',
                'discount_value' => 10,
                'minimum_spend' => 1000,
                'usage_limit' => 200,
                'uses' => 38,
                'starts_at' => now()->subDays(2),
                'ends_at' => now()->addDays(14),
                'status' => 'active',
            ]
        );

        SellerCampaign::updateOrCreate(
            ['seller_id' => $seller->id, 'code' => 'LIKHAE150'],
            [
                'type' => 'voucher',
                'name' => '₱150 Store Voucher',
                'discount_type' => 'fixed',
                'discount_value' => 150,
                'minimum_spend' => 1500,
                'usage_limit' => 100,
                'uses' => 21,
                'starts_at' => now()->subDays(5),
                'ends_at' => now()->addDays(20),
                'status' => 'active',
            ]
        );

        SellerProfile::updateOrCreate(
            ['seller_id' => $seller->id],
            [
                'shop_name' => 'LIKHAE Studio',
                'tagline' => 'Thoughtful local finds for modern Filipino homes.',
                'description' => 'LIKHAE Studio curates practical, beautiful products with dependable seller support.',
                'location' => '24 Rizal Street, Santa Cruz, Laguna 4009',
                'business_days' => 'Monday to Saturday',
                'business_hours' => '9:00 AM – 6:00 PM',
                'processing_days' => 2,
                'order_cutoff' => '2:00 PM',
                'vacation_mode' => false,
                'auto_accept_orders' => false,
                'store_visibility' => true,
            ]
        );

        $notifications = [
            ['orders', 'New order received', '#10001 from Angela Cruz is ready for review.', route('seller.orders', ['mode' => 'show', 'order' => '10001'])],
            ['inventory', 'Low stock alert', 'Mechanical Keyboard has only 3 units remaining.', route('seller.products', ['mode' => 'inventory'])],
            ['finance', 'Payout is being processed', 'Your latest seller balance is being prepared for settlement.', route('seller.finance', ['tab' => 'payouts'])],
            ['system', 'Seller Center connected', 'Your Seller Center pages are now using database-backed actions.', route('seller.dashboard')],
        ];

        foreach ($notifications as $index => $row) {
            WorkspaceNotification::firstOrCreate(
                ['user_id' => $seller->id, 'title' => $row[1]],
                [
                    'type' => $row[0],
                    'body' => $row[2],
                    'action_url' => $row[3],
                    'read_at' => $index > 1 ? now()->subHour() : null,
                    'created_at' => now()->subMinutes(($index + 1) * 18),
                    'updated_at' => now()->subMinutes(($index + 1) * 18),
                ]
            );
        }
    }
}
