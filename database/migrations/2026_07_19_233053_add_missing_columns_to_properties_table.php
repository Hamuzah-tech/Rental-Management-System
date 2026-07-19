<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties', 'monthly_rent')) {
                $table->decimal('monthly_rent', 10, 2)->default(0)->after('description');
            }
            
            if (!Schema::hasColumn('properties', 'max_tenants')) {
                $table->integer('max_tenants')->default(10)->after('monthly_rent');
            }
            
            if (!Schema::hasColumn('properties', 'registration_token')) {
                $table->string('registration_token')->nullable()->after('max_tenants');
            }
            
            if (!Schema::hasColumn('properties', 'status')) {
                $table->boolean('status')->default(true)->after('registration_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['monthly_rent', 'max_tenants', 'registration_token', 'status']);
        });
    }
};