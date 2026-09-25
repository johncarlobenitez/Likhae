<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('first_name', 100);
            $table->string('middle_initial', 10)->nullable();
            $table->string('last_name', 100);

            $table->string('sex', 30);
            $table->string('email')->unique();
            $table->string('contact_number', 32);
            $table->date('birthday');

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();

            $table->enum('status', [
                'PENDING',
                'ACTIVE',
                'SUSPENDED',
                'DEACTIVATED',
            ])->default('PENDING')->index();

            $table->rememberToken();
            $table->timestamps();

            $table->index(['last_name', 'first_name']);
            $table->index('contact_number');
        });

        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('provider', 50);
            $table->string('provider_user_id');
            $table->string('provider_email')->nullable();
            $table->text('avatar_url')->nullable();

            $table->timestamps();

            $table->unique(
                ['provider', 'provider_user_id'],
                'social_provider_user_unique'
            );

            $table->unique(
                ['user_id', 'provider'],
                'social_user_provider_unique'
            );
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();

            $table->foreignId('user_id')
                ->nullable()
                ->index()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();

            $table->morphs('tokenable');

            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();

            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('social_accounts');
        Schema::dropIfExists('users');
    }
};