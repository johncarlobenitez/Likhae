<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('buyer')->after('id');
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('middle_initial', 80)->nullable()->after('last_name');
            $table->string('sex')->nullable()->after('middle_initial');
            $table->date('birthday')->nullable()->after('sex');
            $table->string('contact_number')->nullable()->after('birthday');
            $table->string('province')->nullable()->after('contact_number');
            $table->string('municipality')->nullable()->after('province');
            $table->string('barangay')->nullable()->after('municipality');
            $table->string('house_number')->nullable()->after('barangay');
            $table->string('street')->nullable()->after('house_number');
            $table->string('valid_id_path')->nullable()->after('street');
            $table->string('business_name')->nullable()->after('valid_id_path');
            $table->string('store_name')->nullable()->after('business_name');
            $table->string('line_of_business')->nullable()->after('store_name');
            $table->string('business_type')->nullable()->after('line_of_business');
            $table->string('dti_sec_number')->nullable()->after('business_type');
            $table->string('tin')->nullable()->after('dti_sec_number');
            $table->string('business_permit_path')->nullable()->after('tin');
            $table->string('vehicle_type')->nullable()->after('business_permit_path');
            $table->string('plate_number')->nullable()->after('vehicle_type');
            $table->string('or_cr_path')->nullable()->after('plate_number');
            $table->string('drivers_license_path')->nullable()->after('or_cr_path');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'first_name', 'last_name', 'middle_initial', 'sex', 'birthday',
                'contact_number', 'province', 'municipality', 'barangay', 'house_number',
                'street', 'valid_id_path', 'business_name', 'store_name', 'line_of_business',
                'business_type', 'dti_sec_number', 'tin', 'business_permit_path',
                'vehicle_type', 'plate_number', 'or_cr_path', 'drivers_license_path',
            ]);
        });
    }
};
