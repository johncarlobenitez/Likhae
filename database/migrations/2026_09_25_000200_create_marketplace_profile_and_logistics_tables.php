<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('primary_category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->foreignId('business_address_id')
                ->nullable()
                ->constrained('addresses')
                ->restrictOnDelete();

            $table->string('business_name', 200);
            $table->string('business_registration_number', 120)->nullable();

            $table->enum('status', [
                'PENDING',
                'ACTIVE',
                'SUSPENDED',
                'DEACTIVATED',
            ])->default('PENDING')->index();

            $table->foreignId('approved_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });

        Schema::create('logistics_centers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('owner_user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('address_id')
                ->constrained('addresses')
                ->restrictOnDelete();

            $table->string('code', 50)->unique();
            $table->string('business_name', 200);

            $table->string('business_registration_number', 120)->nullable();
            $table->string('dti_registration_number', 120)->nullable();

            $table->enum('status', [
                'PENDING',
                'ACTIVE',
                'SUSPENDED',
                'DEACTIVATED',
            ])->default('PENDING')->index();

            $table->foreignId('approved_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });

        Schema::create('logistics_center_users', function (Blueprint $table) {
            $table->id();

            $table->foreignId('logistics_center_id')
                ->constrained('logistics_centers')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('position', 80)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('joined_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['logistics_center_id', 'user_id'],
                'logistics_center_user_unique'
            );
        });

        Schema::create('service_areas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('logistics_center_id')
                ->constrained('logistics_centers')
                ->cascadeOnDelete();

            $table->string('name', 150);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(
                ['logistics_center_id', 'name'],
                'service_area_center_name_unique'
            );
        });

        Schema::create('service_area_barangays', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_area_id')
                ->constrained('service_areas')
                ->cascadeOnDelete();

            $table->foreignId('barangay_id')
                ->constrained('geo_barangays')
                ->restrictOnDelete();

            $table->timestamps();

            $table->unique(
                ['service_area_id', 'barangay_id'],
                'service_area_barangay_unique'
            );
        });

        Schema::create('rider_application_data', function (Blueprint $table) {
            $table->id();

            $table->foreignId('registration_application_id')
                ->unique()
                ->constrained('registration_applications')
                ->cascadeOnDelete();

            $table->foreignId('target_logistics_center_id')
                ->constrained('logistics_centers')
                ->restrictOnDelete();

            $table->string('vehicle_type', 80);
            $table->string('plate_number', 50);
            $table->string('drivers_license_number', 100)->nullable();

            $table->timestamps();

            $table->index('plate_number');
        });

        Schema::create('rider_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('logistics_center_id')
                ->constrained('logistics_centers')
                ->restrictOnDelete();

            $table->string('vehicle_type', 80);
            $table->string('plate_number', 50)->unique();
            $table->string('drivers_license_number', 100)->nullable()->unique();

            $table->enum('status', [
                'PENDING',
                'ACTIVE',
                'INACTIVE',
                'SUSPENDED',
                'DEACTIVATED',
            ])->default('PENDING')->index();

            $table->foreignId('approved_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->index(
                ['logistics_center_id', 'status'],
                'rider_center_status_idx'
            );
        });

        Schema::create('rider_service_areas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rider_profile_id')
                ->constrained('rider_profiles')
                ->cascadeOnDelete();

            $table->foreignId('service_area_id')
                ->constrained('service_areas')
                ->cascadeOnDelete();

            $table->foreignId('assigned_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->boolean('is_active')->default(true);
            $table->timestamp('assigned_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['rider_profile_id', 'service_area_id'],
                'rider_service_area_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rider_service_areas');
        Schema::dropIfExists('rider_profiles');
        Schema::dropIfExists('rider_application_data');
        Schema::dropIfExists('service_area_barangays');
        Schema::dropIfExists('service_areas');
        Schema::dropIfExists('logistics_center_users');
        Schema::dropIfExists('logistics_centers');
        Schema::dropIfExists('seller_profiles');
    }
};