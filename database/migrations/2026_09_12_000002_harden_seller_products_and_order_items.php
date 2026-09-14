<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'listing_status')) {
                $table->string('listing_status')->default('active')->after('stock');
            }

            if (! Schema::hasColumn('products', 'admin_status')) {
                $table->string('admin_status')->default('approved')->after('listing_status');
            }

            if (! Schema::hasColumn('products', 'variants')) {
                $table->json('variants')->nullable()->after('image_path');
            }
        });

        if (Schema::hasColumn('products', 'status')) {
            DB::table('products')->whereNull('listing_status')->update(['listing_status' => DB::raw('status')]);
            DB::table('products')->whereNull('admin_status')->update(['admin_status' => 'approved']);
        }

        $this->backfillUniqueSkus();
        $this->addUniqueIndexIfMissing('products', 'products_sku_unique', ['sku']);

        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'product_name')) {
                $table->string('product_name')->nullable()->after('product_id');
            }

            if (! Schema::hasColumn('order_items', 'variant')) {
                $table->string('variant')->nullable()->after('product_name');
            }
        });

        $this->backfillOrderItemProductNames();

        if (DB::getDriverName() === 'mysql') {
            $this->dropForeignIfExists('order_items', 'order_items_product_id_foreign');
            DB::statement('ALTER TABLE order_items MODIFY product_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE order_items ADD CONSTRAINT order_items_product_id_foreign FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL');
        } elseif (DB::getDriverName() !== 'sqlite') {
            Schema::table('order_items', function (Blueprint $table) {
                $table->foreignId('product_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'variant')) {
                $table->dropColumn('variant');
            }

            if (Schema::hasColumn('order_items', 'product_name')) {
                $table->dropColumn('product_name');
            }
        });

        $this->dropIndexIfExists('products', 'products_sku_unique');

        Schema::table('products', function (Blueprint $table) {
            foreach (['variants', 'admin_status', 'listing_status'] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    private function addUniqueIndexIfMissing(string $table, string $index, array $columns): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        $exists = collect(Schema::getIndexes($table))->contains(fn (array $item) => ($item['name'] ?? null) === $index);

        if (! $exists) {
            Schema::table($table, fn (Blueprint $table) => $table->unique($columns, $index));
        }
    }

    private function backfillUniqueSkus(): void
    {
        $seen = [];

        DB::table('products')
            ->select(['id', 'seller_id', 'sku'])
            ->orderBy('id')
            ->get()
            ->each(function (object $product) use (&$seen): void {
                $sku = trim((string) $product->sku);

                if ($sku === '' || isset($seen[strtolower($sku)])) {
                    $sku = 'SLR'.str_pad((string) ($product->seller_id ?: 0), 5, '0', STR_PAD_LEFT)
                        .'-'.str_pad((string) $product->id, 6, '0', STR_PAD_LEFT);

                    DB::table('products')->where('id', $product->id)->update(['sku' => $sku]);
                }

                $seen[strtolower($sku)] = true;
            });
    }

    private function backfillOrderItemProductNames(): void
    {
        if (! Schema::hasColumn('products', 'name')) {
            return;
        }

        DB::table('order_items')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->whereNull('order_items.product_name')
            ->select(['order_items.id', 'products.name as product_name'])
            ->orderBy('order_items.id')
            ->get()
            ->each(function (object $item): void {
                if ($item->product_name !== null) {
                    DB::table('order_items')
                        ->where('id', $item->id)
                        ->update(['product_name' => $item->product_name]);
                }
            });
    }

    private function dropIndexIfExists(string $table, string $index): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        $exists = collect(Schema::getIndexes($table))->contains(fn (array $item) => ($item['name'] ?? null) === $index);

        if ($exists) {
            Schema::table($table, fn (Blueprint $table) => $table->dropUnique($index));
        }
    }

    private function dropForeignIfExists(string $table, string $foreign): void
    {
        $schema = DB::getDatabaseName();
        $exists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', $schema)
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $foreign)
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->exists();

        if ($exists) {
            DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$foreign}");
        }
    }
};
