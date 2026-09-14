<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            if (! Schema::hasColumn('deliveries', 'provider')) {
                $table->string('provider')->nullable()->after('status');
            }

            if (! Schema::hasColumn('deliveries', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->unique()->after('provider');
            }

            if (! Schema::hasColumn('deliveries', 'pickup_window')) {
                $table->string('pickup_window')->nullable()->after('tracking_number');
            }

            if (! Schema::hasColumn('deliveries', 'pickup_note')) {
                $table->text('pickup_note')->nullable()->after('pickup_window');
            }

            if (! Schema::hasColumn('deliveries', 'requested_at')) {
                $table->timestamp('requested_at')->nullable()->after('pickup_note');
            }
        });

        if (! Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->text('body');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                $table->index(['sender_id', 'recipient_id']);
            });
        }

        if (! Schema::hasTable('product_reviews')) {
            Schema::create('product_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
                $table->unsignedTinyInteger('rating');
                $table->text('body');
                $table->text('reply')->nullable();
                $table->timestamp('replied_at')->nullable();
                $table->boolean('has_photo')->default(false);
                $table->timestamps();
                $table->index(['seller_id', 'rating']);
            });
        }

        if (! Schema::hasTable('seller_campaigns')) {
            Schema::create('seller_campaigns', function (Blueprint $table) {
                $table->id();
                $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
                $table->string('type')->default('discount');
                $table->string('name');
                $table->string('code')->nullable();
                $table->string('discount_type')->default('percent');
                $table->decimal('discount_value', 10, 2)->default(0);
                $table->decimal('minimum_spend', 10, 2)->default(0);
                $table->unsignedInteger('usage_limit')->nullable();
                $table->unsignedInteger('uses')->default(0);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->string('status')->default('draft');
                $table->timestamps();
                $table->index(['seller_id', 'type', 'status']);
            });
        }

        if (! Schema::hasTable('seller_profiles')) {
            Schema::create('seller_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('seller_id')->unique()->constrained('users')->cascadeOnDelete();
                $table->string('shop_name')->nullable();
                $table->string('tagline')->nullable();
                $table->text('description')->nullable();
                $table->string('location')->nullable();
                $table->string('business_days')->nullable();
                $table->string('business_hours')->nullable();
                $table->unsignedTinyInteger('processing_days')->default(2);
                $table->string('order_cutoff')->nullable();
                $table->boolean('vacation_mode')->default(false);
                $table->boolean('auto_accept_orders')->default(false);
                $table->boolean('store_visibility')->default(true);
                $table->json('notification_preferences')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('workspace_notifications')) {
            Schema::create('workspace_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('type')->default('system');
                $table->string('title');
                $table->text('body')->nullable();
                $table->string('action_url')->nullable();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'read_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('workspace_notifications');
        Schema::dropIfExists('seller_profiles');
        Schema::dropIfExists('seller_campaigns');
        Schema::dropIfExists('product_reviews');
        Schema::dropIfExists('messages');

        Schema::table('deliveries', function (Blueprint $table) {
            if (Schema::hasColumn('deliveries', 'tracking_number')) {
                $table->dropUnique(['tracking_number']);
            }

            $columns = array_values(array_filter([
                Schema::hasColumn('deliveries', 'provider') ? 'provider' : null,
                Schema::hasColumn('deliveries', 'tracking_number') ? 'tracking_number' : null,
                Schema::hasColumn('deliveries', 'pickup_window') ? 'pickup_window' : null,
                Schema::hasColumn('deliveries', 'pickup_note') ? 'pickup_note' : null,
                Schema::hasColumn('deliveries', 'requested_at') ? 'requested_at' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
