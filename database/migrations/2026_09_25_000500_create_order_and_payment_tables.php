<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_number', 50)->unique();

            $table->foreignId('buyer_user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('source_cart_id')
                ->nullable()
                ->constrained('carts')
                ->nullOnDelete();

            $table->enum('status', [
                'PLACED',
                'PROCESSING',
                'COMPLETED',
                'CANCELLED',
                'PARTIALLY_CANCELLED',
                'RETURNED',
            ])->default('PLACED')->index();

            $table->char('currency', 3)->default('PHP');

            $table->decimal('subtotal', 14, 2);
            $table->decimal('discount_total', 14, 2)->default(0);
            $table->decimal('shipping_total', 14, 2)->default(0);
            $table->decimal('reward_discount_total', 14, 2)->default(0);
            $table->decimal('grand_total', 14, 2);

            $table->enum('payment_status', [
                'UNPAID',
                'PENDING',
                'PAID',
                'FAILED',
                'PARTIALLY_REFUNDED',
                'REFUNDED',
            ])->default('UNPAID')->index();

            $table->timestamp('placed_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->text('cancellation_reason')->nullable();

            $table->timestamps();

            $table->index(
                ['buyer_user_id', 'created_at'],
                'order_buyer_created_idx'
            );
        });

        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->unique()
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignId('barangay_id')
                ->nullable()
                ->constrained('geo_barangays')
                ->nullOnDelete();

            $table->string('recipient_name', 200);
            $table->string('contact_number', 32);

            /*
             * Historical checkout snapshot.
             * These names/codes must NOT change when the buyer later
             * edits their saved address or PSGC reference data changes.
             */
            $table->string('province_code', 30)->nullable();
            $table->string('province_name', 150);

            $table->string('municipality_code', 30)->nullable();
            $table->string('municipality_name', 150);

            $table->string('barangay_code', 30)->nullable();
            $table->string('barangay_name', 150);

            $table->string('postal_code', 20)->nullable();
            $table->string('street_address', 255);
            $table->string('landmark', 255)->nullable();

            $table->timestamps();
        });

        Schema::create('seller_orders', function (Blueprint $table) {
            $table->id();

            $table->string('seller_order_number', 60)->unique();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignId('seller_profile_id')
                ->constrained('seller_profiles')
                ->restrictOnDelete();

            $table->decimal('item_subtotal', 14, 2);
            $table->decimal('discount_total', 14, 2)->default(0);
            $table->decimal('shipping_fee', 14, 2)->default(0);
            $table->decimal('grand_total', 14, 2);

            $table->timestamps();

            $table->unique(
                ['order_id', 'seller_profile_id'],
                'order_seller_unique'
            );

            $table->index(
                ['seller_profile_id', 'created_at'],
                'seller_order_created_idx'
            );
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('seller_order_id')
                ->constrained('seller_orders')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->restrictOnDelete();

            /*
             * Immutable purchase snapshot.
             */
            $table->string('product_name', 200);
            $table->string('sku', 120);

            $table->decimal('unit_price', 14, 2);
            $table->unsignedInteger('quantity');

            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('line_total', 14, 2);

            $table->timestamps();

            $table->index('seller_order_id');
        });

        Schema::create('order_item_options', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_item_id')
                ->constrained('order_items')
                ->cascadeOnDelete();

            $table->string('option_name', 100);
            $table->string('option_value', 150);

            $table->timestamps();

            $table->unique(
                ['order_item_id', 'option_name'],
                'order_item_option_name_unique'
            );
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->enum('method', [
                'COD',
                'ONLINE',
            ]);

            $table->string('provider', 100)->nullable();
            $table->string('provider_reference', 191)->nullable();

            $table->decimal('amount', 14, 2);

            $table->enum('status', [
                'PENDING',
                'AUTHORIZED',
                'PAID',
                'FAILED',
                'CANCELLED',
                'PARTIALLY_REFUNDED',
                'REFUNDED',
            ])->default('PENDING')->index();

            $table->timestamp('initiated_at');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();

            $table->text('failure_reason')->nullable();

            $table->timestamps();

            $table->unique(
                ['provider', 'provider_reference'],
                'payment_provider_reference_unique'
            );

            $table->index(['order_id', 'status']);
        });

        Schema::create('promotion_redemptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('promotion_id')
                ->constrained('promotions')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignId('seller_order_id')
                ->nullable()
                ->constrained('seller_orders')
                ->cascadeOnDelete();

            $table->decimal('discount_amount', 14, 2);

            $table->timestamp('redeemed_at');

            $table->timestamps();

            $table->index(
                ['promotion_id', 'user_id'],
                'promotion_user_redemption_idx'
            );
        });

        Schema::create('order_reward_usages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignId('reward_account_id')
                ->constrained('reward_accounts')
                ->restrictOnDelete();

            $table->unsignedInteger('points_used');
            $table->decimal('discount_amount', 14, 2);

            $table->timestamps();

            $table->unique(
                ['order_id', 'reward_account_id'],
                'order_reward_account_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_reward_usages');
        Schema::dropIfExists('promotion_redemptions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_item_options');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('seller_orders');
        Schema::dropIfExists('order_addresses');
        Schema::dropIfExists('orders');
    }
};