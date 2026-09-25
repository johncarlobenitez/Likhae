<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LikhaeSellerTestAccountsSeeder extends Seeder
{
    private const PASSWORD = 'Password1';

    private const CATEGORIES = [
        'Pet Supplies' => [
            'Dog Food & Treats',
            'Cat Litter & Accessories',
            'Aquariums & Fish Supplies',
            'Bird Feeders & Food',
            'Pet Grooming Products',
            'Pet Health & Wellness',
        ],
        'Electronics and Gadgets' => [
            'Mobile Phones & Accessories',
            'Laptops, Desktops & Monitors',
            'Audio & Video Equipment',
            'Smart Home Devices',
            'Cameras & Photography',
            'Wearable Technology',
        ],
        "Women's Apparel" => [
            'Dresses & Skirts',
            'Tops & Blouses',
            'Activewear & Yoga Pants',
            'Lingerie & Sleepwear',
            'Jackets & Coats',
            'Shoes & Accessories',
        ],
        "Men's Apparel" => [
            'Suits & Blazers',
            'Casual Shirts & Pants',
            'Outerwear & Jackets',
            'Activewear & Fitness Gear',
            'Shoes & Accessories',
            'Grooming Products',
            'T-Shirts',
            'Polo Shirts',
            'Short Sleeve Shirts',
            'Long Sleeve Shirts',
            'Shorts',
            'Jeans',
            'Formal Pants',
            'Casual Pants',
            'Hoodies',
        ],
        'Kids and Baby' => [
            'Baby Clothes & Accessories',
            'Toys & Games',
            'Educational Materials',
            'Strollers & Gear',
            'Nursery Furniture',
            'Safety and Health',
        ],
        'Home and Garden' => [
            'Kitchen Appliances',
            'Furniture & Decor',
            'Gardening Tools',
            'Outdoor Living',
            'Home Improvement Tools',
            'Bedding & Bath',
        ],
        'Sports and Outdoors' => [
            'Fitness Equipment',
            'Camping & Hiking Gear',
            'Sports Apparel',
            'Cycling & Bikes',
            'Water Sports',
            'Team Sports Equipment',
        ],
        'Health and Beauty' => [
            'Skincare Products',
            'Haircare Solutions',
            'Makeup & Cosmetics',
            'Personal Care Appliances',
            "Men's Grooming",
            'Health Supplements',
        ],
        'Books and Media' => [
            'Fiction & Non-Fiction Books',
            'Magazines & Periodicals',
            'Music CDs & Vinyl Records',
            'Movie DVDs & Blu-ray',
            'Video Games & Consoles',
            'Educational DVDs',
        ],
        'Food and Gourmet' => [
            'Baking Supplies & Ingredients',
            'Coffee, Tea & Beverages',
            'Snacks & Candy',
            'Specialty Foods & International Cuisine',
            'Organic and Health Foods',
            'Meal Kits & Prepped Foods',
        ],
        'Furniture and Office Equipment' => [
            'Office Desks & Chairs',
            'Storage Cabinets & Shelving',
            'Conference & Meeting Furniture',
            'Computer Tables & Workstations',
            'Ergonomic Accessories',
            'Office Lighting & Fixtures',
        ],
        'Jewelry and Watches' => [
            'Necklaces & Pendants',
            'Rings & Earrings',
            'Bracelets & Bangles',
            'Watches for Men & Women',
            'Fashion Jewelry',
            'Jewelry Storage & Care',
        ],
    ];

    private const SELLERS = [
        ['email' => 'pet@likhae.com', 'first' => 'Paula', 'last' => 'Santos', 'business' => 'Paw & Whisker Essentials', 'category' => 'Pet Supplies', 'province' => 'Laguna', 'municipality' => 'City of San Pablo', 'barangay' => 'San Antonio', 'postal' => '4000'],
        ['email' => 'electronics@likhae.com', 'first' => 'Enrico', 'last' => 'Reyes', 'business' => 'NovaTech Gadgets', 'category' => 'Electronics and Gadgets', 'province' => 'Metro Manila', 'municipality' => 'Quezon City', 'barangay' => 'Bagumbayan', 'postal' => '1100'],
        ['email' => 'women@likhae.com', 'first' => 'Amara', 'last' => 'Cruz', 'business' => "Amara Women's Wear", 'category' => "Women's Apparel", 'province' => 'Cebu', 'municipality' => 'City of Cebu', 'barangay' => 'Lahug', 'postal' => '6000'],
        ['email' => 'men@likhae.com', 'first' => 'Nolan', 'last' => 'Garcia', 'business' => 'Northline Menswear', 'category' => "Men's Apparel", 'province' => 'Davao del Sur', 'municipality' => 'City of Davao', 'barangay' => 'Poblacion', 'postal' => '8000'],
        ['email' => 'kids@likhae.com', 'first' => 'Kira', 'last' => 'Mendoza', 'business' => 'Little Haven Kids', 'category' => 'Kids and Baby', 'province' => 'Quezon', 'municipality' => 'City of Lucena', 'barangay' => 'Ibabang Dupay', 'postal' => '4301'],
        ['email' => 'home@likhae.com', 'first' => 'Hector', 'last' => 'Navarro', 'business' => 'Casa Verde Home & Garden', 'category' => 'Home and Garden', 'province' => 'Batangas', 'municipality' => 'Lipa City', 'barangay' => 'Mataas na Lupa', 'postal' => '4217'],
        ['email' => 'sports@likhae.com', 'first' => 'Sam', 'last' => 'Torres', 'business' => 'Summit Active Gear', 'category' => 'Sports and Outdoors', 'province' => 'Pampanga', 'municipality' => 'Angeles City', 'barangay' => 'Balibago', 'postal' => '2009'],
        ['email' => 'beauty@likhae.com', 'first' => 'Bianca', 'last' => 'Ramos', 'business' => 'LumiCare Beauty', 'category' => 'Health and Beauty', 'province' => 'Iloilo', 'municipality' => 'Iloilo City', 'barangay' => 'Mandurriao', 'postal' => '5000'],
        ['email' => 'books@likhae.com', 'first' => 'Benedict', 'last' => 'Lim', 'business' => 'Page & Pixel Books', 'category' => 'Books and Media', 'province' => 'Misamis Oriental', 'municipality' => 'Cagayan de Oro City', 'barangay' => 'Lapasan', 'postal' => '9000'],
        ['email' => 'food@likhae.com', 'first' => 'Fiona', 'last' => 'Villanueva', 'business' => 'Harvest Table Gourmet', 'category' => 'Food and Gourmet', 'province' => 'Benguet', 'municipality' => 'Baguio City', 'barangay' => 'Session Road Area', 'postal' => '2600'],
        ['email' => 'office@likhae.com', 'first' => 'Owen', 'last' => 'Diaz', 'business' => 'Workspace Living', 'category' => 'Furniture and Office Equipment', 'province' => 'Rizal', 'municipality' => 'Antipolo City', 'barangay' => 'Dela Paz', 'postal' => '1870'],
        ['email' => 'jewelry@likhae.com', 'first' => 'Julia', 'last' => 'Aquino', 'business' => 'Aurelia Jewelry & Watches', 'category' => 'Jewelry and Watches', 'province' => 'Cavite', 'municipality' => 'Tagaytay City', 'barangay' => 'Mendez Crossing East', 'postal' => '4120'],
    ];

    private const PRODUCTS = [
        'Pet Supplies' => [
            'subcategory' => 'Dog Food & Treats',
            'name' => 'Chicken & Rice Adult Dog Food',
            'description' => 'A balanced dry dog food made for everyday feeding, with chicken flavor, rice, and essential nutrients for adult dogs.',
            'price' => 685.00,
            'image' => 'https://images.unsplash.com/photo-1589924691995-400dc9ecc119?w=1200&q=85&auto=format&fit=crop',
            'specs' => ['Pet Type' => 'Dogs', 'Food Type' => 'Dry kibble', 'Main Ingredient' => 'Chicken and rice', 'Shelf Life' => '12 months'],
            'variations' => [['name' => 'Weight', 'value' => '2kg', 'sku' => 'PET-FOOD-2KG', 'price' => 685.00, 'stock' => 8], ['name' => 'Weight', 'value' => '5kg', 'sku' => 'PET-FOOD-5KG', 'price' => 1490.00, 'stock' => 5]],
        ],
        'Electronics and Gadgets' => [
            'subcategory' => 'Audio & Video Equipment',
            'name' => 'Wireless Noise-Canceling Headphones',
            'description' => 'Over-ear wireless headphones with soft ear cushions, Bluetooth connectivity, and active noise reduction for focused listening.',
            'price' => 2490.00,
            'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=1200&q=85&auto=format&fit=crop',
            'specs' => ['Connectivity' => 'Bluetooth 5.0', 'Battery Life' => 'Up to 30 hours', 'Charging Port' => 'USB-C', 'Warranty' => '6 months service warranty'],
            'variations' => [['name' => 'Color', 'value' => 'Black', 'sku' => 'ELEC-HP-BLK', 'price' => 2490.00, 'stock' => 6], ['name' => 'Color', 'value' => 'Silver', 'sku' => 'ELEC-HP-SLV', 'price' => 2590.00, 'stock' => 4]],
        ],
        "Women's Apparel" => [
            'subcategory' => 'Dresses & Skirts',
            'name' => 'Floral Midi Wrap Dress',
            'description' => 'A lightweight floral wrap dress with a flattering midi length, soft drape, and adjustable waist tie.',
            'price' => 1190.00,
            'image' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=1200&q=85&auto=format&fit=crop',
            'specs' => ['Material' => 'Polyester blend', 'Fit' => 'Regular', 'Care' => 'Hand wash recommended', 'Length' => 'Midi'],
            'variations' => [['name' => 'Size', 'value' => 'Small', 'sku' => 'WOM-DRESS-S', 'price' => 1190.00, 'stock' => 5], ['name' => 'Size', 'value' => 'Medium', 'sku' => 'WOM-DRESS-M', 'price' => 1190.00, 'stock' => 7], ['name' => 'Size', 'value' => 'Large', 'sku' => 'WOM-DRESS-L', 'price' => 1250.00, 'stock' => 3]],
        ],
        "Men's Apparel" => [
            'subcategory' => 'Polo Shirts',
            'name' => 'Classic Cotton Polo Shirt',
            'description' => 'A breathable cotton polo shirt with a clean collar, everyday fit, and durable stitching for casual wear.',
            'price' => 790.00,
            'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=1200&q=85&auto=format&fit=crop',
            'specs' => ['Material' => 'Cotton pique', 'Fit' => 'Regular', 'Sleeve' => 'Short sleeve', 'Care' => 'Machine washable'],
            'variations' => [['name' => 'Size', 'value' => 'Medium', 'sku' => 'MEN-POLO-M', 'price' => 790.00, 'stock' => 10], ['name' => 'Size', 'value' => 'Large', 'sku' => 'MEN-POLO-L', 'price' => 830.00, 'stock' => 8]],
        ],
        'Kids and Baby' => [
            'subcategory' => 'Toys & Games',
            'name' => 'Wooden Shape Sorting Toy',
            'description' => 'A colorful wooden sorting toy that helps toddlers practice shape matching, grip control, and early problem solving.',
            'price' => 540.00,
            'image' => 'https://images.unsplash.com/photo-1566576912321-d58ddd7a6088?w=1200&q=85&auto=format&fit=crop',
            'specs' => ['Material' => 'Painted wood', 'Recommended Age' => '18 months and up', 'Pieces' => '12 shape blocks', 'Safety' => 'Rounded edges'],
            'variations' => [['name' => 'Set', 'value' => 'Classic Shapes', 'sku' => 'KID-SORT-CLS', 'price' => 540.00, 'stock' => 12]],
        ],
        'Home and Garden' => [
            'subcategory' => 'Furniture & Decor',
            'name' => 'Woven Seagrass Storage Basket',
            'description' => 'A handwoven seagrass basket for organizing blankets, laundry, toys, or living room essentials.',
            'price' => 980.00,
            'image' => 'https://images.unsplash.com/photo-1618220179428-22790b461013?w=1200&q=85&auto=format&fit=crop',
            'specs' => ['Material' => 'Natural seagrass', 'Use' => 'Storage and decor', 'Finish' => 'Handwoven', 'Care' => 'Wipe clean with dry cloth'],
            'variations' => [['name' => 'Size', 'value' => 'Medium', 'sku' => 'HOME-BASKET-M', 'price' => 980.00, 'stock' => 9], ['name' => 'Size', 'value' => 'Large', 'sku' => 'HOME-BASKET-L', 'price' => 1290.00, 'stock' => 6]],
        ],
        'Sports and Outdoors' => [
            'subcategory' => 'Fitness Equipment',
            'name' => 'Adjustable Dumbbell Pair',
            'description' => 'Space-saving adjustable dumbbells for home workouts, strength training, and progressive resistance exercises.',
            'price' => 2190.00,
            'image' => 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=1200&q=85&auto=format&fit=crop',
            'specs' => ['Material' => 'Steel and rubber grip', 'Use' => 'Home fitness', 'Includes' => 'Pair of dumbbells', 'Grip' => 'Textured non-slip handle'],
            'variations' => [['name' => 'Weight', 'value' => '10kg Pair', 'sku' => 'SPORT-DB-10', 'price' => 2190.00, 'stock' => 4], ['name' => 'Weight', 'value' => '20kg Pair', 'sku' => 'SPORT-DB-20', 'price' => 3890.00, 'stock' => 2]],
        ],
        'Health and Beauty' => [
            'subcategory' => 'Skincare Products',
            'name' => 'Hydrating Vitamin C Face Serum',
            'description' => 'A lightweight facial serum formulated for daily hydration and a brighter-looking complexion.',
            'price' => 650.00,
            'image' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=1200&q=85&auto=format&fit=crop',
            'specs' => ['Volume' => '30ml', 'Skin Type' => 'Normal to dry', 'Texture' => 'Lightweight serum', 'Use' => 'Morning or evening routine'],
            'variations' => [['name' => 'Bottle Size', 'value' => '30ml', 'sku' => 'BEAUTY-SERUM-30', 'price' => 650.00, 'stock' => 15]],
        ],
        'Books and Media' => [
            'subcategory' => 'Fiction & Non-Fiction Books',
            'name' => 'Creative Journaling Workbook',
            'description' => 'A guided workbook with prompts, reflection pages, and creative exercises for daily journaling practice.',
            'price' => 420.00,
            'image' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=1200&q=85&auto=format&fit=crop',
            'specs' => ['Format' => 'Paperback', 'Pages' => '160 pages', 'Language' => 'English', 'Paper' => 'Cream book paper'],
            'variations' => [['name' => 'Cover', 'value' => 'Softcover', 'sku' => 'BOOK-JOURNAL-SOFT', 'price' => 420.00, 'stock' => 20]],
        ],
        'Food and Gourmet' => [
            'subcategory' => 'Coffee, Tea & Beverages',
            'name' => 'Benguet Arabica Coffee Beans',
            'description' => 'Whole Arabica coffee beans grown in Benguet, roasted for a smooth cup with nutty and chocolate notes.',
            'price' => 520.00,
            'image' => 'https://images.unsplash.com/photo-1447933601403-0c6688de566e?w=1200&q=85&auto=format&fit=crop',
            'specs' => ['Origin' => 'Benguet, Philippines', 'Roast' => 'Medium', 'Form' => 'Whole beans', 'Flavor Notes' => 'Nutty, cocoa, mild citrus'],
            'variations' => [['name' => 'Weight', 'value' => '250g', 'sku' => 'FOOD-COFFEE-250', 'price' => 520.00, 'stock' => 18], ['name' => 'Weight', 'value' => '500g', 'sku' => 'FOOD-COFFEE-500', 'price' => 940.00, 'stock' => 9]],
        ],
        'Furniture and Office Equipment' => [
            'subcategory' => 'Office Desks & Chairs',
            'name' => 'Ergonomic Mesh Office Chair',
            'description' => 'A breathable mesh office chair with adjustable height, lumbar support, and smooth-rolling casters.',
            'price' => 3490.00,
            'image' => 'https://images.unsplash.com/photo-1580480055273-228ff5388ef8?w=1200&q=85&auto=format&fit=crop',
            'specs' => ['Material' => 'Mesh back and cushioned seat', 'Adjustments' => 'Height and tilt tension', 'Weight Capacity' => 'Up to 110kg', 'Assembly' => 'Required'],
            'variations' => [['name' => 'Color', 'value' => 'Black', 'sku' => 'OFFICE-CHAIR-BLK', 'price' => 3490.00, 'stock' => 5], ['name' => 'Color', 'value' => 'Gray', 'sku' => 'OFFICE-CHAIR-GRY', 'price' => 3590.00, 'stock' => 3]],
        ],
        'Jewelry and Watches' => [
            'subcategory' => 'Necklaces & Pendants',
            'name' => 'Gold-Plated Pearl Pendant Necklace',
            'description' => 'A delicate gold-plated necklace with a freshwater-style pearl pendant for everyday or occasion wear.',
            'price' => 890.00,
            'image' => 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=1200&q=85&auto=format&fit=crop',
            'specs' => ['Material' => 'Gold-plated alloy', 'Pendant' => 'Pearl-style charm', 'Chain Length' => '45cm', 'Care' => 'Keep dry and store separately'],
            'variations' => [['name' => 'Finish', 'value' => 'Gold', 'sku' => 'JEWEL-PEARL-GLD', 'price' => 890.00, 'stock' => 11]],
        ],
    ];

    public function run(): void
    {
        DB::transaction(function (): void {
            $categories = $this->seedCategories();
            $buyerRole = DB::table('roles')->where('code', 'buyer')->value('id');
            $sellerRole = DB::table('roles')->where('code', 'seller')->value('id');
            $adminId = DB::table('users')->where('email', 'admin@likhae.com')->value('id');
            $barangayId = DB::table('geo_barangays')->where('code', 'PH-DEV-BRGY')->value('id');

            foreach (self::SELLERS as $index => $row) {
                $now = now();
                $userId = $this->upsert('users', ['email' => $row['email']], [
                    'first_name' => $row['first'], 'last_name' => $row['last'], 'middle_initial' => chr(65 + $index),
                    'sex' => $index % 2 === 0 ? 'FEMALE' : 'MALE',
                    'birthday' => now()->subYears(30 + $index)->subMonths($index % 12)->toDateString(),
                    'contact_number' => '0917'.str_pad((string) (1000000 + $index), 7, '0', STR_PAD_LEFT),
                    'password' => Hash::make(self::PASSWORD), 'status' => 'ACTIVE', 'email_verified_at' => $now,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
                foreach ([$buyerRole, $sellerRole] as $roleId) {
                    DB::table('user_roles')->updateOrInsert(['user_id' => $userId, 'role_id' => $roleId], [
                        'assigned_by_user_id' => $adminId, 'is_active' => true, 'assigned_at' => $now,
                        'revoked_at' => null, 'created_at' => $now, 'updated_at' => $now,
                    ]);
                }
                $addressId = $this->upsert('addresses', ['user_id' => $userId, 'label' => 'Seller pickup'], [
                    'barangay_id' => $barangayId, 'recipient_name' => $row['first'].' '.$row['last'],
                    'contact_number' => '0917'.str_pad((string) (1000000 + $index), 7, '0', STR_PAD_LEFT),
                    'street_address' => 'Unit '.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT).' LIKHAE Test Street',
                    'landmark' => $row['municipality'].', '.$row['province'].' '.$row['postal'],
                    'latitude' => null, 'longitude' => null, 'is_default' => true, 'created_at' => $now, 'updated_at' => $now,
                ]);
                $profileId = $this->upsert('seller_profiles', ['user_id' => $userId], [
                    'primary_category_id' => $categories[$row['category']], 'business_address_id' => $addressId,
                    'business_name' => $row['business'], 'business_registration_number' => 'DEV-DTI-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                    'status' => 'ACTIVE', 'approved_by_user_id' => $adminId, 'approved_at' => $now,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
                $this->seedProduct($profileId, $row['category']);
            }
        });
    }

    private function seedCategories(): array
    {
        $ids = [];

        foreach (self::CATEGORIES as $category => $children) {
            $parentId = $this->upsert('categories', ['slug' => Str::slug($category)], [
                'name' => $category, 'parent_id' => null, 'description' => null, 'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $ids[$category] = $parentId;

            foreach ($children as $child) {
                $this->upsert('categories', ['slug' => $this->categorySlug($child, $parentId)], [
                    'name' => $child, 'parent_id' => $parentId, 'description' => null, 'is_active' => true,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }

        return $ids;
    }

    private function categorySlug(string $name, int $parentId): string
    {
        return Str::slug($name).'-'.$parentId;
    }

    private function seedProduct(int $sellerProfileId, string $categoryName): void
    {
        $data = self::PRODUCTS[$categoryName] ?? null;

        if (! $data) {
            return;
        }

        $parentId = DB::table('categories')->where('slug', Str::slug($categoryName))->value('id');
        $categoryId = DB::table('categories')->where('parent_id', $parentId)->where('name', $data['subcategory'])->value('id');
        $slug = Str::slug($data['name']);
        $productId = $this->upsert('products', ['seller_profile_id' => $sellerProfileId, 'slug' => $slug], [
            'category_id' => $categoryId, 'name' => $data['name'], 'description' => $data['description'],
            'base_price' => $data['price'], 'status' => 'ACTIVE', 'published_at' => now(), 'archived_at' => null,
            'deleted_at' => null, 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('product_images')->updateOrInsert(['product_id' => $productId, 'file_path' => $data['image']], [
            'alt_text' => $data['name'], 'is_primary' => true, 'sort_order' => 0, 'created_at' => now(), 'updated_at' => now(),
        ]);

        DB::table('product_variant_values')->whereIn('product_variant_id', DB::table('product_variants')->where('product_id', $productId)->select('id'))->delete();
        DB::table('inventories')->whereIn('product_variant_id', DB::table('product_variants')->where('product_id', $productId)->select('id'))->delete();
        DB::table('product_variants')->where('product_id', $productId)->delete();
        foreach ($data['variations'] as $variation) {
            $price = $variation['price'] ?? $data['price'];
            $variantId = DB::table('product_variants')->insertGetId([
                'product_id' => $productId, 'sku' => $variation['sku'], 'price' => $price,
                'is_default' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('inventories')->insert(['product_variant_id' => $variantId, 'quantity_on_hand' => (int) ($variation['stock'] ?? 0), 'quantity_reserved' => 0, 'reorder_level' => 0, 'created_at' => now(), 'updated_at' => now()]);
        }

        DB::table('product_specifications')->where('product_id', $productId)->delete();
        foreach ($data['specs'] as $name => $value) {
            DB::table('product_specifications')->insert(['product_id' => $productId, 'name' => $name, 'value' => $value, 'sort_order' => 0, 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    private function upsert(string $table, array $identity, array $values): int
    {
        DB::table($table)->updateOrInsert($identity, $values);
        return (int) DB::table($table)->where($identity)->value('id');
    }
}
