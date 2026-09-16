<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            if (! Schema::hasColumn('deliveries', 'pickup_rider_id')) {
                $table->foreignId('pickup_rider_id')->nullable()->after('rider_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('deliveries', 'pickup_assigned_at')) {
                $table->timestamp('pickup_assigned_at')->nullable()->after('requested_at');
            }

            if (! Schema::hasColumn('deliveries', 'pickup_accepted_at')) {
                $table->timestamp('pickup_accepted_at')->nullable()->after('pickup_assigned_at');
            }

            if (! Schema::hasColumn('deliveries', 'picked_up_at')) {
                $table->timestamp('picked_up_at')->nullable()->after('pickup_accepted_at');
            }

            if (! Schema::hasColumn('deliveries', 'arrived_at_sorting_center_at')) {
                $table->timestamp('arrived_at_sorting_center_at')->nullable()->after('picked_up_at');
            }

            if (! Schema::hasColumn('deliveries', 'delivery_picked_up_at')) {
                $table->timestamp('delivery_picked_up_at')->nullable()->after('assigned_at');
            }

            if (! Schema::hasColumn('deliveries', 'failed_at')) {
                $table->timestamp('failed_at')->nullable()->after('delivered_at');
            }

            if (! Schema::hasColumn('deliveries', 'failure_reason')) {
                $table->text('failure_reason')->nullable()->after('failed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            foreach ([
                'failure_reason',
                'failed_at',
                'delivery_picked_up_at',
                'arrived_at_sorting_center_at',
                'picked_up_at',
                'pickup_accepted_at',
                'pickup_assigned_at',
            ] as $column) {
                if (Schema::hasColumn('deliveries', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (Schema::hasColumn('deliveries', 'pickup_rider_id')) {
                $table->dropConstrainedForeignId('pickup_rider_id');
            }
        });
    }
};
