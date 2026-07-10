<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoveOutNoticesTable extends Migration
{
    public function up(): void
    {
        Schema::create('move_out_notices', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('notice_type', [
                'End of this Month',
                'End of Semester',
                'Specific Date',
                'Other'
            ]);

            $table->string('semester')->nullable();

            $table->date('specific_date')->nullable();

            $table->text('comment')->nullable();

            $table->enum('status', ['Pending', 'Confirmed', 'Cancelled'])->default('Pending');

            $table->timestamp('submitted_at');

            $table->timestamp('confirmed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('move_out_notices');
    }
}