<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('seller_profile_id')
                ->constrained('seller_profiles')
                ->restrictOnDelete();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->string('name', 200);
            $table->string('slug', 220);
            $table->text('description')->nullable();

            $table->decimal('base_price', 14, 2);

            $table->enum('status', [
                'DRAFT',
                'ACTIVE',
                'ARCHIVED',
                'SUSPENDED',
            ])->default('DRAFT')->index();

            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                ['seller_profile_id', 'slug'],
                'product_seller_slug_unique'
            );

            $table->index(
                ['seller_profile_id', 'status'],
                'product_seller_status_idx'
            );

            $table->index(['category_id', 'status']);
        });

        Schema::create('product_specifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->string('name', 150);
            $table->text('value');

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->unique(
                ['product_id', 'name'],
                'product_specification_name_unique'
            );
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->string('file_path', 500);
            $table->string('alt_text', 255)->nullable();

            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(
                ['product_id', 'is_primary'],
                'product_image_primary_idx'
            );
        });

        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->string('name', 100);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->unique(
                ['product_id', 'name'],
                'product_attribute_name_unique'
            );
        });

        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_attribute_id')
                ->constrained('product_attributes')
                ->cascadeOnDelete();

            $table->string('value', 150);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->unique(
                ['product_attribute_id', 'value'],
                'product_attribute_value_unique'
            );
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->string('sku', 120)->unique();
            $table->decimal('price', 14, 2);

            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(
                ['product_id', 'is_active'],
                'product_variant_active_idx'
            );
        });

        Schema::create('product_variant_values', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->cascadeOnDelete();

            $table->foreignId('product_attribute_value_id')
                ->constrained('product_attribute_values')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                ['product_variant_id', 'product_attribute_value_id'],
                'product_variant_attribute_value_unique'
            );
        });

        Schema::create('inventories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_variant_id')
                ->unique()
                ->constrained('product_variants')
                ->cascadeOnDelete();

            $table->unsignedInteger('quantity_on_hand')->default(0);
            $table->unsignedInteger('quantity_reserved')->default(0);
            $table->unsignedInteger('reorder_level')->default(0);

            $table->timestamps();
        });

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->restrictOnDelete();

            $table->enum('movement_type', [
                'STOCK_IN',
                'STOCK_OUT',
                'RESERVE',
                'RELEASE',
                'SALE',
                'RETURN',
                'ADJUSTMENT',
            ]);

            $table->integer('quantity_delta');

            $table->string('reference_type', 80)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->foreignId('performed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamp('occurred_at');

            $table->index(
                ['product_variant_id', 'occurred_at'],
                'inventory_variant_occurred_idx'
            );

            $table->index(
                ['reference_type', 'reference_id'],
                'inventory_reference_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('product_variant_values');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('product_attributes');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_specifications');
        Schema::dropIfExists('products');
    }
};