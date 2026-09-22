<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->enum('payment_status', ['pending', 'paid', 'refunded', 'partially_refunded'])->default('pending')->change();
        });

        Schema::table('delivery_events', function (Blueprint $table): void {
            $table->enum('status', ['unassigned', 'assigned', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered', 'failed', 'returned'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('payment_status')->default('pending')->change();
        });

        Schema::table('delivery_events', function (Blueprint $table): void {
            $table->string('status')->change();
        });
    }
};
