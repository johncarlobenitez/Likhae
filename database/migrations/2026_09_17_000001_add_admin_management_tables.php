<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('admin_policies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('revision')->nullable();
            $table->longText('body');
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['status', 'published_at']);
        });

        Schema::create('admin_announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->string('audience')->default('all');
            $table->string('priority')->default('standard');
            $table->string('status')->default('draft');
            $table->timestamp('publish_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['status', 'publish_at']);
        });

        Schema::create('admin_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->json('notification_preferences')->nullable();
            $table->timestamps();
        });

        Schema::create('admin_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('target_type')->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['target_type', 'target_id']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_audit_logs');
        Schema::dropIfExists('admin_preferences');
        Schema::dropIfExists('admin_announcements');
        Schema::dropIfExists('admin_policies');
        Schema::dropIfExists('platform_settings');
    }
};
