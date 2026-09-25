<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            $table->enum('type', [
                'DIRECT',
                'ORDER',
                'SUPPORT',
                'LOGISTICS',
            ])->default('DIRECT');

            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();

            $table->foreignId('seller_order_id')
                ->nullable()
                ->constrained('seller_orders')
                ->nullOnDelete();

            $table->foreignId('shipment_id')
                ->nullable()
                ->constrained('shipments')
                ->nullOnDelete();

            $table->foreignId('created_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['type', 'updated_at']);
        });

        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('conversation_id')
                ->constrained('conversations')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->timestamp('last_read_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['conversation_id', 'user_id'],
                'conversation_user_unique'
            );
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('conversation_id')
                ->constrained('conversations')
                ->cascadeOnDelete();

            $table->foreignId('sender_user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->text('body')->nullable();

            $table->timestamp('sent_at');
            $table->timestamp('edited_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['conversation_id', 'sent_at'],
                'message_conversation_sent_idx'
            );
        });

        Schema::create('message_attachments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('message_id')
                ->constrained('messages')
                ->cascadeOnDelete();

            $table->string('file_path', 500);
            $table->string('original_name', 255);
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();

            $table->timestamps();
        });

        Schema::create('disputes', function (Blueprint $table) {
            $table->id();

            $table->string('dispute_number', 60)->unique();

            $table->foreignId('opened_by_user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('assigned_admin_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->restrictOnDelete();

            $table->foreignId('seller_order_id')
                ->nullable()
                ->constrained('seller_orders')
                ->restrictOnDelete();

            $table->foreignId('shipment_id')
                ->nullable()
                ->constrained('shipments')
                ->restrictOnDelete();

            $table->string('type', 100);
            $table->string('subject', 200);
            $table->text('description');

            $table->enum('status', [
                'OPEN',
                'UNDER_REVIEW',
                'WAITING_FOR_PARTY',
                'RESOLVED',
                'CLOSED',
            ])->default('OPEN')->index();

            $table->timestamp('opened_at');
            $table->timestamp('resolved_at')->nullable();

            $table->text('resolution')->nullable();

            $table->timestamps();

            $table->index(
                ['assigned_admin_user_id', 'status'],
                'dispute_admin_status_idx'
            );
        });

        Schema::create('dispute_evidence', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dispute_id')
                ->constrained('disputes')
                ->cascadeOnDelete();

            $table->foreignId('uploaded_by_user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('file_path', 500);
            $table->string('original_name', 255)->nullable();
            $table->string('mime_type', 120)->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });

        Schema::create('seller_compliance_cases', function (Blueprint $table) {
            $table->id();

            $table->string('case_number', 60)->unique();

            $table->foreignId('seller_profile_id')
                ->constrained('seller_profiles')
                ->restrictOnDelete();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->restrictOnDelete();

            $table->foreignId('opened_by_admin_user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('violation_type', 120);
            $table->text('description');

            $table->enum('status', [
                'OPEN',
                'UNDER_REVIEW',
                'WARNED',
                'SUSPENDED',
                'RESOLVED',
                'DISMISSED',
            ])->default('OPEN')->index();

            $table->timestamp('opened_at');
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();

            $table->index(
                ['seller_profile_id', 'status'],
                'seller_compliance_status_idx'
            );
        });

        Schema::create('seller_compliance_actions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('seller_compliance_case_id')
                ->constrained('seller_compliance_cases')
                ->cascadeOnDelete();

            $table->foreignId('performed_by_user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->enum('action_type', [
                'WARNING',
                'PRODUCT_SUSPEND',
                'SELLER_SUSPEND',
                'SELLER_REACTIVATE',
                'NOTE',
            ]);

            $table->text('reason')->nullable();
            $table->timestamp('performed_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_compliance_actions');
        Schema::dropIfExists('seller_compliance_cases');
        Schema::dropIfExists('dispute_evidence');
        Schema::dropIfExists('disputes');
        Schema::dropIfExists('message_attachments');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversation_participants');
        Schema::dropIfExists('conversations');
    }
};