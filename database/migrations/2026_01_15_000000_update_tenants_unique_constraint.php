<?php
// database/migrations/2026_01_15_000000_update_tenants_unique_constraint.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the existing unique index on phone
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropUnique('tenants_phone_unique');
        });

        // Add composite unique constraint on (property_id, phone)
        Schema::table('tenants', function (Blueprint $table) {
            $table->unique(['property_id', 'phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropUnique('tenants_property_id_phone_unique');
            $table->unique('phone');
        });
    }
};