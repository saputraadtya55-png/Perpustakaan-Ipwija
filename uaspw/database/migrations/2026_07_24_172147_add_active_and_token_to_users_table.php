<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pengecekan dilakukan langsung di luar Schema::table agar aman dari error pendeteksian kolom
        if (!Schema::hasColumn('users', 'is_active') || !Schema::hasColumn('users', 'verification_token')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'is_active')) {
                    $table->boolean('is_active')->default(false)->after('password');
                }

                if (!Schema::hasColumn('users', 'verification_token')) {
                    $table->string('verification_token')->nullable()->after('is_active');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('users', 'verification_token')) {
                $table->dropColumn('verification_token');
            }
        });
    }
};