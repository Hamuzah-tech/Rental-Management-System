<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            $table->foreignId('tenant_id')
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('payment_month');
            $table->decimal('amount', 10, 2);

            $table->string('screenshot')->nullable();

            $table->enum('status', [
                'Pending',
                'Approved',
                'Rejected'
            ])->default('Pending');

            $table->text('remarks')->nullable();

            $table->timestamp('submitted_at')->nullable();

            $table->timestamp('approved_at')->nullable();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['approved_by']);

            $table->dropColumn([
                'tenant_id',
                'payment_month',
                'amount',
                'screenshot',
                'status',
                'remarks',
                'submitted_at',
                'approved_at',
                'approved_by'
            ]);
        });
    }
};