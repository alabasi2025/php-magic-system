<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('owner_type', 100); // customer, supplier, employee, user
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('entity_id');
            $table->enum('wallet_type', ['personal', 'business', 'savings'])->default('personal');
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('available_balance', 15, 2)->default(0);
            $table->decimal('reserved_balance', 15, 2)->default(0);
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'suspended', 'blocked'])->default('active');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['owner_type', 'owner_id'], 'idx_wallets_owner');
            $table->index('entity_id', 'idx_wallets_entity');
            $table->index('status', 'idx_wallets_status');
            $table->index('is_active', 'idx_wallets_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallets');
    }
};
