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
        Schema::create('warehouse_groups', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('Unique warehouse group code');
            $table->string('name', 200)->comment('Warehouse group name');
            $table->text('description')->nullable()->comment('Group description');
            $table->foreignId('account_id')->nullable()->constrained('chart_accounts')->nullOnDelete()->comment('Linked accounting account');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // Indexes
            $table->index('code');
            $table->index('status');
            $table->index('account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_groups');
    }
};
