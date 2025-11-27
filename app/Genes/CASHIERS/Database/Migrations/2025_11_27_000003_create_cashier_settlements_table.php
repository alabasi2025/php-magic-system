<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cashier_settlements', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->unsignedBigInteger('cashier_id');
            $table->unsignedBigInteger('entity_id');
            $table->date('settlement_date');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('total_deposits', 15, 2)->default(0);
            $table->decimal('total_withdrawals', 15, 2)->default(0);
            $table->decimal('expected_balance', 15, 2)->default(0);
            $table->decimal('actual_balance', 15, 2)->default(0);
            $table->decimal('difference', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('cashier_id', 'idx_cashsettle_cashier');
            $table->index('entity_id', 'idx_cashsettle_entity');
            $table->index('settlement_date', 'idx_cashsettle_date');
            $table->index('status', 'idx_cashsettle_status');
            $table->unique(['cashier_id', 'settlement_date'], 'unq_cashier_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cashier_settlements');
    }
};
