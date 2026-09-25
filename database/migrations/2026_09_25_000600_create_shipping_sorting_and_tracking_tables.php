<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $shipmentStatuses = [
        'PLACED',
        'CONFIRMED',
        'PREPARING',
        'READY_FOR_PICKUP',
        'PICKED_UP',
        'AT_SORTING_CENTER',
        'SORTED',
        'ASSIGNED_TO_RIDER',
        'OUT_FOR_DELIVERY',
        'DELIVERED',
        'COMPLETED',
        'DELIVERY_FAILED',
        'RETURNED',
    ];

    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('seller_order_id')
                ->unique()
                ->constrained('seller_orders')
                ->cascadeOnDelete();

            $table->string('tracking_number', 80)->unique();

            $table->foreignId('logistics_center_id')
                ->nullable()
                ->constrained('logistics_centers')
                ->restrictOnDelete();

            $table->foreignId('service_area_id')
                ->nullable()
                ->constrained('service_areas')
                ->restrictOnDelete();

            $table->foreignId('destination_barangay_id')
                ->nullable()
                ->constrained('geo_barangays')
                ->restrictOnDelete();

            $table->enum('current_status', [
                'PLACED',
                'CONFIRMED',
                'PREPARING',
                'READY_FOR_PICKUP',
                'PICKED_UP',
                'AT_SORTING_CENTER',
                'SORTED',
                'ASSIGNED_TO_RIDER',
                'OUT_FOR_DELIVERY',
                'DELIVERED',
                'COMPLETED',
                'DELIVERY_FAILED',
                'RETURNED',
            ])->default('PLACED')->index();

            $table->timestamps();

            $table->index(
                ['logistics_center_id', 'current_status'],
                'shipment_center_status_idx'
            );

            $table->index(
                ['service_area_id', 'current_status'],
                'shipment_area_status_idx'
            );
        });

        Schema::create('pickup_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shipment_id')
                ->unique()
                ->constrained('shipments')
                ->cascadeOnDelete();

            $table->foreignId('requested_by_user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->enum('handover_method', [
                'RIDER_PICKUP',
                'SELLER_DROPOFF',
            ]);

            $table->enum('status', [
                'PENDING',
                'APPROVED',
                'REJECTED',
                'ASSIGNED',
                'COMPLETED',
                'CANCELLED',
            ])->default('PENDING')->index();

            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('requested_at');
            $table->timestamp('reviewed_at')->nullable();

            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });

        Schema::create('pickup_request_addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pickup_request_id')
                ->unique()
                ->constrained('pickup_requests')
                ->cascadeOnDelete();

            $table->string('contact_name', 200);
            $table->string('contact_number', 32);

            $table->string('province_code', 30)->nullable();
            $table->string('province_name', 150);

            $table->string('municipality_code', 30)->nullable();
            $table->string('municipality_name', 150);

            $table->string('barangay_code', 30)->nullable();
            $table->string('barangay_name', 150);

            $table->string('postal_code', 20)->nullable();
            $table->string('street_address', 255);
            $table->string('landmark', 255)->nullable();

            $table->timestamps();
        });

        Schema::create('sorting_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shipment_id')
                ->unique()
                ->constrained('shipments')
                ->cascadeOnDelete();

            $table->foreignId('logistics_center_id')
                ->constrained('logistics_centers')
                ->restrictOnDelete();

            $table->foreignId('received_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('received_at')->nullable();

            $table->foreignId('service_area_id')
                ->nullable()
                ->constrained('service_areas')
                ->restrictOnDelete();

            $table->foreignId('sorted_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('sorted_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(
                ['logistics_center_id', 'received_at'],
                'sorting_center_received_idx'
            );
        });

        Schema::create('shipment_rider_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shipment_id')
                ->constrained('shipments')
                ->cascadeOnDelete();

            $table->foreignId('rider_profile_id')
                ->constrained('rider_profiles')
                ->restrictOnDelete();

            $table->enum('assignment_type', [
                'PICKUP',
                'DELIVERY',
            ]);

            $table->enum('status', [
                'ASSIGNED',
                'ACCEPTED',
                'REJECTED',
                'IN_PROGRESS',
                'COMPLETED',
                'CANCELLED',
            ])->default('ASSIGNED')->index();

            $table->foreignId('assigned_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('assigned_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            $table->index(
                ['shipment_id', 'assignment_type', 'status'],
                'shipment_assignment_type_status_idx'
            );

            $table->index(
                ['rider_profile_id', 'assignment_type', 'status'],
                'rider_assignment_work_idx'
            );
        });

        Schema::create('waybills', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shipment_id')
                ->unique()
                ->constrained('shipments')
                ->cascadeOnDelete();

            /*
             * QR/barcode contents are derived from shipments.tracking_number.
             * Do not create fake or independent parcel codes.
             */
            $table->foreignId('generated_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->unsignedInteger('format_version')->default(1);
            $table->string('label_path', 500)->nullable();

            $table->timestamp('generated_at');

            $table->timestamps();
        });

        Schema::create('parcel_scans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shipment_id')
                ->constrained('shipments')
                ->cascadeOnDelete();

            $table->foreignId('scanned_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('logistics_center_id')
                ->nullable()
                ->constrained('logistics_centers')
                ->restrictOnDelete();

            $table->foreignId('rider_assignment_id')
                ->nullable()
                ->constrained('shipment_rider_assignments')
                ->nullOnDelete();

            $table->enum('scan_type', [
                'PICKUP_CONFIRM',
                'SORTING_CENTER_RECEIVE',
                'SORTING',
                'SORTING_CENTER_RELEASE',
                'DELIVERY_CONFIRM',
                'RETURN_RECEIVE',
                'OTHER',
            ]);

            $table->enum('scan_method', [
                'QR',
                'BARCODE',
                'MANUAL',
            ]);

            $table->string('scanned_code', 191);

            $table->enum('result', [
                'MATCH',
                'MISMATCH',
                'INVALID',
            ]);

            $table->text('notes')->nullable();
            $table->timestamp('scanned_at');

            $table->timestamps();

            $table->index('scanned_code');
            $table->index(['shipment_id', 'scanned_at']);
        });

        Schema::create('shipment_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shipment_id')
                ->constrained('shipments')
                ->cascadeOnDelete();

            $table->enum('status', [
                'PLACED',
                'CONFIRMED',
                'PREPARING',
                'READY_FOR_PICKUP',
                'PICKED_UP',
                'AT_SORTING_CENTER',
                'SORTED',
                'ASSIGNED_TO_RIDER',
                'OUT_FOR_DELIVERY',
                'DELIVERED',
                'COMPLETED',
                'DELIVERY_FAILED',
                'RETURNED',
            ]);

            $table->foreignId('actor_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('logistics_center_id')
                ->nullable()
                ->constrained('logistics_centers')
                ->restrictOnDelete();

            $table->foreignId('rider_assignment_id')
                ->nullable()
                ->constrained('shipment_rider_assignments')
                ->nullOnDelete();

            $table->text('notes')->nullable();
            $table->timestamp('occurred_at');

            $table->timestamps();

            $table->index(
                ['shipment_id', 'occurred_at'],
                'shipment_event_timeline_idx'
            );

            $table->index(['status', 'occurred_at']);
        });

        Schema::create('delivery_attempts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shipment_id')
                ->constrained('shipments')
                ->cascadeOnDelete();

            $table->foreignId('rider_assignment_id')
                ->constrained('shipment_rider_assignments')
                ->restrictOnDelete();

            $table->unsignedInteger('attempt_number');

            $table->enum('status', [
                'SUCCESS',
                'FAILED',
                'RESCHEDULED',
            ]);

            $table->text('failure_reason')->nullable();
            $table->string('proof_path', 500)->nullable();

            $table->timestamp('attempted_at');
            $table->timestamp('next_attempt_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['shipment_id', 'attempt_number'],
                'delivery_attempt_number_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_attempts');
        Schema::dropIfExists('shipment_events');
        Schema::dropIfExists('parcel_scans');
        Schema::dropIfExists('waybills');
        Schema::dropIfExists('shipment_rider_assignments');
        Schema::dropIfExists('sorting_records');
        Schema::dropIfExists('pickup_request_addresses');
        Schema::dropIfExists('pickup_requests');
        Schema::dropIfExists('shipments');
    }
};