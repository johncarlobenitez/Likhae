<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('id')->constrained('categories')->nullOnDelete();
            }

            if (! Schema::hasColumn('categories', 'created_by_user_id')) {
                $table->foreignId('created_by_user_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('categories', 'created_by_seller_id')) {
                $table->foreignId('created_by_seller_id')->nullable()->after('created_by_user_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('categories', 'source')) {
                $table->string('source')->nullable()->after('created_by_seller_id');
            }
        });

        Schema::table('seller_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('seller_profiles', 'line_of_business_category_id')) {
                $table->foreignId('line_of_business_category_id')->nullable()->after('seller_id')->constrained('categories')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('seller_profiles', 'line_of_business_category_id')) {
                $table->dropForeign(['line_of_business_category_id']);
                $table->dropColumn('line_of_business_category_id');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            foreach (['created_by_seller_id', 'created_by_user_id', 'parent_id'] as $column) {
                if (Schema::hasColumn('categories', $column)) {
                    $table->dropForeign([$column]);
                }
            }

            $columns = array_values(array_filter([
                Schema::hasColumn('categories', 'source') ? 'source' : null,
                Schema::hasColumn('categories', 'created_by_seller_id') ? 'created_by_seller_id' : null,
                Schema::hasColumn('categories', 'created_by_user_id') ? 'created_by_user_id' : null,
                Schema::hasColumn('categories', 'parent_id') ? 'parent_id' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
