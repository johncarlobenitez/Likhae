<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();

            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->text('description')->nullable();

            $table->timestamps();
        });

        DB::table('roles')->insert([
            [
                'code' => 'buyer',
                'name' => 'Buyer',
                'description' => 'Customer/buyer account role.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'seller',
                'name' => 'Seller',
                'description' => 'Marketplace seller role.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'admin',
                'name' => 'Administrator',
                'description' => 'Platform administrator role.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'logistics',
                'name' => 'Logistics',
                'description' => 'Logistics/sorting center role.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'rider',
                'name' => 'Rider',
                'description' => 'Pickup and delivery rider role.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('role_id')
                ->constrained('roles')
                ->restrictOnDelete();

            $table->foreignId('assigned_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->boolean('is_active')->default(true);
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('revoked_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['user_id', 'role_id'],
                'user_role_unique'
            );
        });

        Schema::create('geo_provinces', function (Blueprint $table) {
            $table->id();

            $table->string('code', 30)->unique();
            $table->string('name', 150)->index();

            $table->timestamps();
        });

        Schema::create('geo_municipalities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('province_id')
                ->constrained('geo_provinces')
                ->cascadeOnDelete();

            $table->string('code', 30)->unique();
            $table->string('name', 150);
            $table->string('type', 30)->nullable();

            $table->timestamps();

            $table->index(
                ['province_id', 'name'],
                'geo_municipality_province_name_idx'
            );
        });

        Schema::create('geo_barangays', function (Blueprint $table) {
            $table->id();

            $table->foreignId('municipality_id')
                ->constrained('geo_municipalities')
                ->cascadeOnDelete();

            $table->string('code', 30)->unique();
            $table->string('name', 150);
            $table->string('postal_code', 20)->nullable();

            $table->timestamps();

            $table->index(
                ['municipality_id', 'name'],
                'geo_barangay_municipality_name_idx'
            );
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('barangay_id')
                ->constrained('geo_barangays')
                ->restrictOnDelete();

            $table->string('label', 50)->nullable();

            $table->string('recipient_name', 200);
            $table->string('contact_number', 32);

            $table->string('street_address', 255);
            $table->string('landmark', 255)->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->boolean('is_default')->default(false);

            $table->timestamps();

            $table->index(['user_id', 'is_default']);
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->string('name', 150);
            $table->string('slug', 180)->unique();
            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['parent_id', 'is_active']);
        });

        Schema::create('user_status_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('changed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('old_status', 30)->nullable();
            $table->string('new_status', 30);
            $table->text('reason')->nullable();

            $table->timestamp('changed_at');

            $table->index(['user_id', 'changed_at']);
        });

        Schema::create('registration_applications', function (Blueprint $table) {
            $table->id();

            $table->string('application_number', 50)->unique();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('role_id')
                ->constrained('roles')
                ->restrictOnDelete();

            $table->enum('status', [
                'PENDING',
                'UNDER_REVIEW',
                'APPROVED',
                'REJECTED',
                'WITHDRAWN',
            ])->default('PENDING')->index();

            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('submitted_at');
            $table->timestamp('reviewed_at')->nullable();

            $table->text('decision_notes')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            $table->index(
                ['user_id', 'role_id', 'status'],
                'registration_user_role_status_idx'
            );
        });

        Schema::create('application_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('registration_application_id')
                ->constrained('registration_applications')
                ->cascadeOnDelete();

            $table->string('document_type', 80);
            $table->string('file_path', 500);
            $table->string('original_name', 255)->nullable();
            $table->string('mime_type', 120)->nullable();

            $table->enum('verification_status', [
                'PENDING',
                'VERIFIED',
                'REJECTED',
            ])->default('PENDING');

            $table->foreignId('verified_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            $table->index(
                ['registration_application_id', 'document_type'],
                'application_document_type_idx'
            );
        });

        Schema::create('seller_application_data', function (Blueprint $table) {
            $table->id();

            $table->foreignId('registration_application_id')
                ->unique()
                ->constrained('registration_applications')
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->string('business_name', 200);
            $table->string('business_registration_number', 120)->nullable();

            $table->timestamps();
        });

        Schema::create('logistics_application_data', function (Blueprint $table) {
            $table->id();

            $table->foreignId('registration_application_id')
                ->unique()
                ->constrained('registration_applications')
                ->cascadeOnDelete();

            $table->foreignId('business_address_id')
                ->constrained('addresses')
                ->restrictOnDelete();

            $table->string('business_name', 200);
            $table->string('business_registration_number', 120)->nullable();
            $table->string('dti_registration_number', 120)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_application_data');
        Schema::dropIfExists('seller_application_data');
        Schema::dropIfExists('application_documents');
        Schema::dropIfExists('registration_applications');
        Schema::dropIfExists('user_status_histories');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('geo_barangays');
        Schema::dropIfExists('geo_municipalities');
        Schema::dropIfExists('geo_provinces');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('roles');
    }
};