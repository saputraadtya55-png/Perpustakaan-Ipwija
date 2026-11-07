<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'otp_code')) {
                $table->string('otp_code')->nullable();
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->string('is_active')->default('pending');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['otp_code', 'is_active']);
        });
    }
};