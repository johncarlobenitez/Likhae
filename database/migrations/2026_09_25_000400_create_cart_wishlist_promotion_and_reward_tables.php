<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('status', [
                'ACTIVE',
                'CONVERTED',
                'ABANDONED',
            ])->default('ACTIVE')->index();

            $table->timestamp('converted_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cart_id')
                ->constrained('carts')
                ->cascadeOnDelete();

            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->restrictOnDelete();

            $table->unsignedInteger('quantity');

            $table->timestamps();

            $table->unique(
                ['cart_id', 'product_variant_id'],
                'cart_variant_unique'
            );
        });

        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
        });

        Schema::create('wishlist_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('wishlist_id')
                ->constrained('wishlists')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                ['wishlist_id', 'product_id'],
                'wishlist_product_unique'
            );
        });

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('seller_profile_id')
                ->nullable()
                ->constrained('seller_profiles')
                ->nullOnDelete();

            $table->foreignId('created_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('code', 80)->unique();
            $table->string('name', 150);

            $table->enum('discount_type', [
                'PERCENT',
                'FIXED',
            ]);

            $table->decimal('discount_value', 14, 2);
            $table->decimal('minimum_order_amount', 14, 2)->default(0);
            $table->decimal('maximum_discount_amount', 14, 2)->nullable();

            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('per_user_limit')->nullable();

            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(
                ['seller_profile_id', 'is_active'],
                'promotion_seller_active_idx'
            );

            $table->index(['starts_at', 'ends_at']);
        });

        Schema::create('promotion_products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('promotion_id')
                ->constrained('promotions')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                ['promotion_id', 'product_id'],
                'promotion_product_unique'
            );
        });

        Schema::create('reward_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('status', [
                'ACTIVE',
                'SUSPENDED',
                'CLOSED',
            ])->default('ACTIVE');

            $table->timestamps();
        });

        Schema::create('reward_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reward_account_id')
                ->constrained('reward_accounts')
                ->cascadeOnDelete();

            $table->enum('transaction_type', [
                'EARN',
                'REDEEM',
                'REVERSAL',
                'EXPIRE',
                'ADJUSTMENT',
            ]);

            $table->integer('points_delta');

            $table->string('reference_type', 80)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->text('description')->nullable();

            $table->timestamp('occurred_at');
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();

            $table->index(
                ['reward_account_id', 'occurred_at'],
                'reward_account_occurred_idx'
            );

            $table->index(
                ['reference_type', 'reference_id'],
                'reward_reference_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_transactions');
        Schema::dropIfExists('reward_accounts');
        Schema::dropIfExists('promotion_products');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('wishlist_items');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};