<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_item_id')
                ->unique()
                ->constrained('order_items')
                ->cascadeOnDelete();

            $table->foreignId('buyer_user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();

            $table->enum('status', [
                'PUBLISHED',
                'HIDDEN',
                'FLAGGED',
            ])->default('PUBLISHED');

            $table->timestamps();

            $table->index(['buyer_user_id', 'created_at']);
        });

        Schema::create('review_responses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('review_id')
                ->unique()
                ->constrained('reviews')
                ->cascadeOnDelete();

            $table->foreignId('seller_profile_id')
                ->constrained('seller_profiles')
                ->restrictOnDelete();

            $table->text('body');
            $table->timestamp('responded_at');

            $table->timestamps();
        });

        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();

            $table->string('return_number', 60)->unique();

            $table->foreignId('seller_order_id')
                ->constrained('seller_orders')
                ->restrictOnDelete();

            $table->foreignId('shipment_id')
                ->nullable()
                ->constrained('shipments')
                ->restrictOnDelete();

            $table->foreignId('buyer_user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('reason_category', 100);
            $table->text('reason_details')->nullable();

            $table->enum('status', [
                'REQUESTED',
                'APPROVED',
                'REJECTED',
                'IN_TRANSIT',
                'RECEIVED',
                'REFUND_PENDING',
                'REFUNDED',
                'CLOSED',
                'CANCELLED',
            ])->default('REQUESTED')->index();

            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('requested_at');
            $table->timestamp('reviewed_at')->nullable();

            $table->text('resolution_notes')->nullable();

            $table->timestamps();

            $table->index(
                ['seller_order_id', 'status'],
                'return_seller_order_status_idx'
            );
        });

        Schema::create('return_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('return_request_id')
                ->constrained('return_requests')
                ->cascadeOnDelete();

            $table->foreignId('order_item_id')
                ->constrained('order_items')
                ->restrictOnDelete();

            $table->unsignedInteger('quantity');
            $table->text('condition_notes')->nullable();

            $table->timestamps();

            $table->unique(
                ['return_request_id', 'order_item_id'],
                'return_request_item_unique'
            );
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();

            $table->string('refund_number', 60)->unique();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->restrictOnDelete();

            $table->foreignId('seller_order_id')
                ->nullable()
                ->constrained('seller_orders')
                ->restrictOnDelete();

            $table->foreignId('return_request_id')
                ->nullable()
                ->constrained('return_requests')
                ->restrictOnDelete();

            $table->foreignId('payment_id')
                ->nullable()
                ->constrained('payments')
                ->restrictOnDelete();

            $table->decimal('amount', 14, 2);

            $table->enum('status', [
                'PENDING',
                'PROCESSING',
                'COMPLETED',
                'FAILED',
                'CANCELLED',
            ])->default('PENDING')->index();

            $table->string('provider_reference', 191)->nullable();

            $table->foreignId('processed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('requested_at');
            $table->timestamp('processed_at')->nullable();

            $table->text('failure_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('return_items');
        Schema::dropIfExists('return_requests');
        Schema::dropIfExists('review_responses');
        Schema::dropIfExists('reviews');
    }
};