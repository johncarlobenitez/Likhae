<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('seller_profiles', 'avatar_path')) {
                $table->string('avatar_path')->nullable()->after('description');
            }

            if (! Schema::hasColumn('seller_profiles', 'banner_path')) {
                $table->string('banner_path')->nullable()->after('avatar_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('seller_profiles', 'banner_path')) {
                $table->dropColumn('banner_path');
            }

            if (Schema::hasColumn('seller_profiles', 'avatar_path')) {
                $table->dropColumn('avatar_path');
            }
        });
    }
};
