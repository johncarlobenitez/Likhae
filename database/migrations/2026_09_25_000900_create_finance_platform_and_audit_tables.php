<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('seller_order_id')
                ->unique()
                ->constrained('seller_orders')
                ->restrictOnDelete();

            /*
             * 0.1000 = 10%.
             * Stored as the historical commission-rate snapshot.
             */
            $table->decimal('commission_rate', 5, 4)
                ->default('0.1000');

            $table->decimal('commissionable_amount', 14, 2);
            $table->decimal('commission_amount', 14, 2);

            $table->enum('status', [
                'PENDING',
                'EARNED',
                'SETTLED',
                'VOID',
            ])->default('PENDING')->index();

            $table->timestamp('calculated_at');
            $table->timestamp('settled_at')->nullable();

            $table->timestamps();
        });

        Schema::create('rider_earnings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rider_assignment_id')
                ->unique()
                ->constrained('shipment_rider_assignments')
                ->restrictOnDelete();

            $table->foreignId('rider_profile_id')
                ->constrained('rider_profiles')
                ->restrictOnDelete();

            $table->decimal('amount', 14, 2);

            $table->enum('status', [
                'PENDING',
                'EARNED',
                'PAID',
                'VOID',
            ])->default('PENDING')->index();

            $table->timestamp('earned_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index(
                ['rider_profile_id', 'status'],
                'rider_earning_status_idx'
            );
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('created_by_user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('title', 200);
            $table->longText('body');

            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['is_active', 'published_at']);
        });

        Schema::create('platform_policies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('published_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('title', 200);
            $table->string('version', 50);
            $table->longText('body');

            $table->timestamp('effective_at');
            $table->timestamp('published_at')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(
                ['title', 'version'],
                'platform_policy_title_version_unique'
            );
        });

        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();

            $table->string('key', 150)->unique();
            $table->text('value')->nullable();

            $table->enum('value_type', [
                'STRING',
                'INTEGER',
                'DECIMAL',
                'BOOLEAN',
            ])->default('STRING');

            $table->foreignId('updated_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });

        DB::table('platform_settings')->insert([
            [
                'key' => 'commission_rate',
                'value' => '0.10',
                'value_type' => 'DECIMAL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'default_currency',
                'value' => 'PHP',
                'value_type' => 'STRING',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('type');

            $table->morphs('notifiable');

            $table->text('data');

            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('actor_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('event', 120);

            $table->string('auditable_type', 150)->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();

            /*
             * Audit snapshots are intentionally semi-structured historical data.
             * They are not master transactional entities.
             */
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(
                ['auditable_type', 'auditable_id'],
                'audit_auditable_idx'
            );

            $table->index(
                ['actor_user_id', 'created_at'],
                'audit_actor_created_idx'
            );

            $table->index(['event', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('platform_settings');
        Schema::dropIfExists('platform_policies');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('rider_earnings');
        Schema::dropIfExists('commission_transactions');
    }
};