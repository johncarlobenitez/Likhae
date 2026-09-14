<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->string('path');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->index(['product_id', 'sort_order']);
            });
        }

        if (! Schema::hasTable('product_specifications')) {
            Schema::create('product_specifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->string('name', 120);
                $table->string('value', 500);
                $table->timestamps();
                $table->unique(['product_id', 'name'], 'product_specification_unique');
            });
        }

        Schema::table('cart_items', function (Blueprint $table) {
            if (! Schema::hasColumn('cart_items', 'product_variation_id')) {
                $table->foreignId('product_variation_id')
                    ->nullable()
                    ->after('product_id')
                    ->constrained('product_variations')
                    ->nullOnDelete();
                $table->index(['cart_id', 'product_variation_id'], 'cart_variation_lookup');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'product_variation_id')) {
                $table->dropConstrainedForeignId('product_variation_id');
            }
        });

        Schema::dropIfExists('product_specifications');
        Schema::dropIfExists('product_images');
    }
};
