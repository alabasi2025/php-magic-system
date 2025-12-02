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
        Schema::create('alabasi_partnership_shares', function (Blueprint $table) {
            // Primary Key
            $table->bigIncrements('id');

            // Quintuple Structure Foreign Keys and Indexes
            $table->unsignedBigInteger('holding_id')->index();
            $table->unsignedBigInteger('unit_id')->index();
            $table->unsignedBigInteger('project_id')->index();

            // Data Columns
            $table->decimal('share_percentage', 8, 2);

            // Auditing Columns (created_by, updated_by)
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Timestamps and Soft Deletes
            $table->timestamps();
            $table->softDeletes();

            // Foreign Key Constraints
            // Assuming standard table names for the quintuple structure

            // Foreign Keys for Auditing (assuming 'users' table)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alabasi_partnership_shares');
    }
};
