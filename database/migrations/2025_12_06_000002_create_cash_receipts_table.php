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
        Schema::create('cash_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->date('receipt_date');
            $table->morphs('account'); // account_id, account_type (CashBox or BankAccount)
            $table->string('received_from');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->default('cash'); // cash, check, transfer, card
            $table->string('check_number')->nullable();
            $table->date('check_date')->nullable();
            $table->string('check_bank')->nullable();
            $table->string('reference_number')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'posted', 'cancelled'])->default('draft');
            $table->unsignedBigInteger('journal_entry_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->json('ai_suggestions')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('set null');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('receipt_date');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_receipts');
    }
};
