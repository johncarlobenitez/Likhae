<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('carts')) {
            Schema::create('carts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('buyer_id')->unique()->constrained('users')->cascadeOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('cart_items')) {
            Schema::create('cart_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->string('variant')->default('Standard');
                $table->unsignedInteger('quantity')->default(1);
                $table->timestamps();
                $table->unique(['cart_id', 'product_id', 'variant'], 'cart_product_variant_unique');
                $table->index(['product_id', 'cart_id']);
            });
        }

        if (! Schema::hasTable('wishlist_items')) {
            Schema::create('wishlist_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['buyer_id', 'product_id']);
                $table->index(['product_id', 'buyer_id']);
            });
        }

        if (! Schema::hasTable('buyer_addresses')) {
            Schema::create('buyer_addresses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
                $table->string('label')->default('Home');
                $table->string('recipient_name');
                $table->string('contact_number', 40);
                $table->string('region')->nullable();
                $table->string('province')->nullable();
                $table->string('municipality')->nullable();
                $table->string('barangay')->nullable();
                $table->string('house_number')->nullable();
                $table->string('street')->nullable();
                $table->string('postal_code', 20)->nullable();
                $table->string('landmark')->nullable();
                $table->boolean('is_default')->default(false);
                $table->timestamps();
                $table->index(['buyer_id', 'is_default']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('buyer_addresses');
        Schema::dropIfExists('wishlist_items');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};
