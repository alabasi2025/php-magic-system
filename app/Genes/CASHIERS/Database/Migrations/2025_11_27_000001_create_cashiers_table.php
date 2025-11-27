<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cashiers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 200);
            $table->string('name_en', 200)->nullable();
            $table->unsignedBigInteger('entity_id');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('safe_id')->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->decimal('max_transaction_limit', 15, 2)->nullable();
            $table->decimal('daily_limit', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('entity_id', 'idx_cashiers_entity');
            $table->index('branch_id', 'idx_cashiers_branch');
            $table->index('user_id', 'idx_cashiers_user');
            $table->index('status', 'idx_cashiers_status');
            $table->index('is_active', 'idx_cashiers_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cashiers');
    }
};
