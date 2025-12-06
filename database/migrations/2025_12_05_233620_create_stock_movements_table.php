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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('movement_number', 100)->unique()->comment('Unique movement reference number');
            $table->enum('movement_type', ['stock_in', 'stock_out', 'transfer', 'adjustment', 'return'])->comment('Type of stock movement');
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete()->comment('Source/destination warehouse');
            $table->foreignId('to_warehouse_id')->nullable()->constrained('warehouses')->restrictOnDelete()->comment('Destination warehouse (for transfers)');
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete()->comment('Item being moved');
            $table->decimal('quantity', 15, 2)->comment('Quantity moved (positive or negative for adjustments)');
            $table->decimal('unit_cost', 15, 2)->default(0)->comment('Cost per unit at time of movement');
            $table->decimal('total_cost', 15, 2)->default(0)->comment('Total cost (quantity * unit_cost)');
            $table->date('movement_date')->comment('Date of movement');
            $table->unsignedBigInteger('reference_id')->nullable()->comment('Reference to related document (PO, SO, etc.)');
            $table->string('reference_type', 100)->nullable()->comment('Type of reference document');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->comment('User who approved the movement');
            $table->timestamp('approved_at')->nullable()->comment('Approval timestamp');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete()->comment('Related accounting journal entry');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete()->comment('User who created the movement');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('movement_number');
            $table->index('movement_type');
            $table->index('warehouse_id');
            $table->index('to_warehouse_id');
            $table->index('item_id');
            $table->index('movement_date');
            $table->index('status');
            $table->index('created_by');
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
