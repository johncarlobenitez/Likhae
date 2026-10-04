<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_refund_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('request_number', 60)->unique();
            $table->foreignId('dispute_id')->unique()->constrained('disputes')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('buyer_user_id')->constrained('users')->restrictOnDelete();
            $table->string('issue_category', 100);
            $table->string('issue_reason', 150);
            $table->string('solution', 50);
            $table->text('description');
            $table->string('refund_method', 100)->nullable();
            $table->decimal('refundable_amount', 12, 2);
            $table->decimal('requested_amount', 12, 2);
            $table->string('buyer_email', 255);
            $table->string('status', 40)->default('REQUEST_SUBMITTED');
            $table->timestamp('submitted_at');
            $table->timestamps();

            $table->index(['buyer_user_id', 'status']);
            $table->index(['order_id', 'status']);
        });

        Schema::create('return_refund_request_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('return_refund_request_id')->constrained('return_refund_requests')->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained('order_items')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('refundable_amount', 12, 2);
            $table->timestamps();

            $table->unique(['return_refund_request_id', 'order_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_refund_request_items');
        Schema::dropIfExists('return_refund_requests');
    }
};
