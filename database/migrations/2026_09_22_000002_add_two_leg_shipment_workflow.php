<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE shipments MODIFY status VARCHAR(40) NOT NULL DEFAULT 'unassigned'");
        DB::statement("ALTER TABLE delivery_events MODIFY status VARCHAR(40) NOT NULL");
        Schema::table('shipments', function (Blueprint $table): void {
            $table->foreignId('pickup_rider_id')->nullable()->after('rider_id')->constrained('riders')->nullOnDelete();
            $table->foreignId('delivery_rider_id')->nullable()->after('pickup_rider_id')->constrained('riders')->nullOnDelete();
        });
        DB::table('shipments')->whereNotNull('rider_id')->whereIn('status', ['unassigned', 'assigned', 'picked_up'])
            ->update(['pickup_rider_id' => DB::raw('rider_id')]);
        DB::table('shipments')->whereNotNull('rider_id')->whereIn('status', ['in_transit', 'out_for_delivery', 'delivered', 'failed', 'returned'])
            ->update(['delivery_rider_id' => DB::raw('rider_id')]);
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('delivery_rider_id');
            $table->dropConstrainedForeignId('pickup_rider_id');
        });
    }
};
