<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('region', 120)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('landmark')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->index(['role', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropIndex(['role', 'status', 'created_at']);
            $table->dropColumn(['region', 'postal_code', 'landmark', 'reviewed_by', 'reviewed_at', 'rejection_reason']);
        });
    }
};
