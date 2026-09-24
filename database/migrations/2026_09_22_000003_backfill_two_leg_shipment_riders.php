<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('shipments')
            ->whereNull('delivery_rider_id')
            ->whereNotNull('rider_id')
            ->whereIn('status', [
                'delivery_assigned', 'delivery_accepted', 'delivery_collected',
                'in_transit', 'out_for_delivery', 'delivered', 'failed', 'returned',
            ])
            ->update(['delivery_rider_id' => DB::raw('rider_id')]);

        DB::table('shipments')
            ->whereNull('pickup_rider_id')
            ->whereNotNull('rider_id')
            ->whereIn('status', [
                'assigned', 'pickup_assigned', 'pickup_accepted',
                'picked_up', 'in_transit_to_hub',
            ])
            ->update(['pickup_rider_id' => DB::raw('rider_id')]);
    }

    public function down(): void
    {
        // Assignment ownership is historical workflow data and must not be erased.
    }
};
