<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cashier_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->unsignedBigInteger('cashier_id');
            $table->unsignedBigInteger('entity_id');
            $table->enum('transaction_type', ['deposit', 'withdrawal', 'transfer'])->default('deposit');
            $table->decimal('amount', 15, 2);
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->decimal('exchange_rate', 10, 4)->default(1);
            $table->decimal('amount_in_base_currency', 15, 2);
            $table->string('reference_type', 100)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('transaction_date');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('cashier_id', 'idx_cashtrans_cashier');
            $table->index('entity_id', 'idx_cashtrans_entity');
            $table->index('transaction_type', 'idx_cashtrans_type');
            $table->index('status', 'idx_cashtrans_status');
            $table->index('transaction_date', 'idx_cashtrans_date');
            $table->index(['reference_type', 'reference_id'], 'idx_cashtrans_ref');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cashier_transactions');
    }
};
