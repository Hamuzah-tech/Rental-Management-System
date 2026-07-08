<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {

            if (!Schema::hasColumn('tenants', 'monthly_rent')) {
                $table->decimal('monthly_rent', 10, 2)->nullable();
            }

            if (!Schema::hasColumn('tenants', 'move_in_date')) {
                $table->date('move_in_date')->nullable();
            }

            if (!Schema::hasColumn('tenants', 'status')) {
                $table->string('status')->default('Active');
            }

        });
    }


    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {

            $columns = [
                'monthly_rent',
                'move_in_date',
                'status'
            ];

            foreach($columns as $column){

                if(Schema::hasColumn('tenants',$column)){
                    $table->dropColumn($column);
                }

            }

        });
    }
};