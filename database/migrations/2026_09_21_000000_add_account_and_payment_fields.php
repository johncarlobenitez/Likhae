<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'is_suspended')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->boolean('is_suspended')->default(false)->after('status');
                $table->index(['is_suspended', 'status'], 'users_suspension_status_index');
            });
        }

        if (! Schema::hasColumn('orders', 'payment_status')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->string('payment_status')->default('pending')->after('payment_method');
                $table->index('payment_status', 'orders_payment_status_index');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'payment_status')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->dropIndex('orders_payment_status_index');
                $table->dropColumn('payment_status');
            });
        }

        if (Schema::hasColumn('users', 'is_suspended')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropIndex('users_suspension_status_index');
                $table->dropColumn('is_suspended');
            });
        }
    }
};
