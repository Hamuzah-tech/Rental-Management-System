<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->ulid('public_id')->nullable()->unique()->after('id');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->ulid('public_id')->nullable()->unique()->after('id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->ulid('public_id')->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('public_id');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('public_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('public_id');
        });
    }
};