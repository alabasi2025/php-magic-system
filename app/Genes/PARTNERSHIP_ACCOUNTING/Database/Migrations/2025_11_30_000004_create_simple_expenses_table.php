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
        Schema::create('alabasi_simple_expenses', function (Blueprint $table) {
            // 1. Primary Key
            $table->id();

            // 2. Quinary Structure (Foreign Keys)
            $table->unsignedBigInteger('holding_id')->index();
            $table->unsignedBigInteger('unit_id')->index();
            $table->unsignedBigInteger('project_id')->index();

            // 3. Table-Specific Columns
            $table->decimal('amount', 15, 2);
            $table->string('description')->nullable();
            $table->date('expense_date');
            $table->string('status', 50)->default('pending');

            // 4. Auditing Fields
            $table->unsignedBigInteger('created_by')->index();
            $table->unsignedBigInteger('updated_by')->index();

            // 5. Timestamps and Soft Deletes
            $table->timestamps();
            $table->softDeletes();

            // 6. Foreign Key Constraints (Assuming standard table names)
            $table->foreign('holding_id')->references('id')->on('holdings')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alabasi_simple_expenses');
    }
};
