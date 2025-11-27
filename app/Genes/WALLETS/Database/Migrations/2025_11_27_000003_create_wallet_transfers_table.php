<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wallet_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->unsignedBigInteger('from_wallet_id');
            $table->unsignedBigInteger('to_wallet_id');
            $table->unsignedBigInteger('entity_id');
            $table->decimal('amount', 15, 2);
            $table->decimal('exchange_rate', 10, 4)->default(1);
            $table->decimal('amount_received', 15, 2);
            $table->decimal('fees', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('transfer_date');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('from_wallet_id', 'idx_wallettrans_from');
            $table->index('to_wallet_id', 'idx_wallettrans_to');
            $table->index('entity_id', 'idx_wallettrans_entity');
            $table->index('status', 'idx_wallettrans_status');
            $table->index('transfer_date', 'idx_wallettrans_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallet_transfers');
    }
};
