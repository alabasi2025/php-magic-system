<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->unsignedBigInteger('wallet_id');
            $table->unsignedBigInteger('entity_id');
            $table->enum('transaction_type', ['credit', 'debit', 'transfer', 'refund'])->default('credit');
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('reference_type', 100)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('transaction_date');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('wallet_id', 'idx_walletrans_wallet');
            $table->index('entity_id', 'idx_walletrans_entity');
            $table->index('transaction_type', 'idx_walletrans_type');
            $table->index('status', 'idx_walletrans_status');
            $table->index('transaction_date', 'idx_walletrans_date');
            $table->index(['reference_type', 'reference_id'], 'idx_walletrans_ref');
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
