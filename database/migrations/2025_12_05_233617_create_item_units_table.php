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
        Schema::create('item_units', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('Unique unit code');
            $table->string('name', 100)->comment('Unit name (Arabic)');
            $table->string('name_en', 100)->nullable()->comment('Unit name (English)');
            $table->string('symbol', 20)->nullable()->comment('Unit symbol (e.g., kg, L, pcs)');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_units');
    }
};
