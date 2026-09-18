<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('handover_method')->nullable()->after('tracking_number');
            $table->foreignId('logistics_center_id')->nullable()->after('handover_method')->constrained('users')->nullOnDelete();
            $table->text('seller_address')->nullable()->after('address');
            $table->timestamp('waybill_generated_at')->nullable()->after('requested_at');
            $table->timestamp('received_at')->nullable()->after('arrived_at_sorting_center_at');
            $table->foreignId('received_by')->nullable()->after('received_at')->constrained('users')->nullOnDelete();
            $table->timestamp('sorted_at')->nullable()->after('received_by');
            $table->string('delivery_area')->nullable()->after('sorted_at');
        });

        Schema::create('parcel_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries')->cascadeOnDelete();
            $table->foreignId('rider_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('assignment_type');
            $table->string('status')->default('assigned');
            $table->timestamp('assigned_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['delivery_id', 'assignment_type']);
            $table->index(['rider_id', 'assignment_type', 'status']);
        });

        Schema::create('parcel_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('previous_status')->nullable();
            $table->string('new_status');
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('performed_by_role')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['delivery_id', 'created_at']);
        });

        Schema::create('parcel_scan_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries')->cascadeOnDelete();
            $table->string('tracking_number');
            $table->string('scan_type');
            $table->foreignId('scanned_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('scanned_by_role');
            $table->foreignId('logistics_center_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assignment_id')->nullable()->constrained('parcel_assignments')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['delivery_id', 'scan_type']);
        });

        DB::table('deliveries')->orderBy('id')->each(function ($delivery): void {
            if ($delivery->pickup_rider_id) {
                DB::table('parcel_assignments')->insertOrIgnore([
                    'delivery_id' => $delivery->id,
                    'rider_id' => $delivery->pickup_rider_id,
                    'assignment_type' => 'seller_pickup',
                    'status' => $delivery->picked_up_at ? 'completed' : ($delivery->pickup_accepted_at ? 'accepted' : 'assigned'),
                    'assigned_at' => $delivery->pickup_assigned_at ?: $delivery->created_at,
                    'accepted_at' => $delivery->pickup_accepted_at,
                    'picked_up_at' => $delivery->picked_up_at,
                    'completed_at' => $delivery->arrived_at_sorting_center_at,
                    'created_at' => $delivery->created_at,
                    'updated_at' => $delivery->updated_at,
                ]);
            }

            if ($delivery->rider_id) {
                DB::table('parcel_assignments')->insertOrIgnore([
                    'delivery_id' => $delivery->id,
                    'rider_id' => $delivery->rider_id,
                    'assignment_type' => 'final_delivery',
                    'status' => $delivery->delivered_at ? 'completed' : ($delivery->delivery_picked_up_at ? 'accepted' : 'assigned'),
                    'assigned_at' => $delivery->assigned_at ?: $delivery->created_at,
                    'accepted_at' => $delivery->delivery_picked_up_at,
                    'picked_up_at' => $delivery->delivery_picked_up_at,
                    'completed_at' => $delivery->delivered_at,
                    'created_at' => $delivery->created_at,
                    'updated_at' => $delivery->updated_at,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcel_scan_events');
        Schema::dropIfExists('parcel_status_histories');
        Schema::dropIfExists('parcel_assignments');

        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('received_by');
            $table->dropConstrainedForeignId('logistics_center_id');
            $table->dropColumn(['handover_method', 'seller_address', 'waybill_generated_at', 'received_at', 'sorted_at', 'delivery_area']);
        });
    }
};
