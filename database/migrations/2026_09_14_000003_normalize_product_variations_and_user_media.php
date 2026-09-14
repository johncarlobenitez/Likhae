<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_variations')) {
            Schema::create('product_variations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->string('name', 80);
                $table->string('value', 160);
                $table->string('sku')->nullable();
                $table->unsignedInteger('stock')->nullable();
                $table->timestamps();
                $table->unique(['product_id', 'name', 'value'], 'product_variation_unique');
                $table->index(['product_id', 'name']);
            });
        }

        if (Schema::hasColumn('products', 'variants')) {
            DB::table('products')
                ->select(['id', 'variants'])
                ->whereNotNull('variants')
                ->orderBy('id')
                ->get()
                ->each(function (object $product): void {
                    $variants = json_decode((string) $product->variants, true);

                    if (! is_array($variants)) {
                        return;
                    }

                    foreach ($variants as $name => $options) {
                        foreach ((array) $options as $option) {
                            $name = trim((string) $name);
                            $value = trim((string) $option);

                            if ($name === '' || $value === '') {
                                continue;
                            }

                            DB::table('product_variations')->updateOrInsert(
                                [
                                    'product_id' => $product->id,
                                    'name' => $name,
                                    'value' => $value,
                                ],
                                [
                                    'updated_at' => now(),
                                    'created_at' => now(),
                                ]
                            );
                        }
                    }
                });

            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('variants');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'profile_photo_path')) {
                $table->string('profile_photo_path')->nullable()->after('valid_id_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'profile_photo_path')) {
                $table->dropColumn('profile_photo_path');
            }
        });

        if (! Schema::hasColumn('products', 'variants')) {
            Schema::table('products', function (Blueprint $table) {
                $table->json('variants')->nullable()->after('image_path');
            });
        }

        if (Schema::hasTable('product_variations')) {
            DB::table('product_variations')
                ->select(['product_id', 'name', 'value'])
                ->orderBy('product_id')
                ->get()
                ->groupBy('product_id')
                ->each(function ($rows, int $productId): void {
                    $variants = [];

                    foreach ($rows as $row) {
                        $variants[$row->name][] = $row->value;
                    }

                    DB::table('products')
                        ->where('id', $productId)
                        ->update(['variants' => json_encode($variants)]);
                });
        }

        Schema::dropIfExists('product_variations');
    }
};
