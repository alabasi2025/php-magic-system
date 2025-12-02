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
        Schema::create('client_genes', function (Blueprint $table) {
            $table->id();
            $table->string('client_name')->comment('اسم العميل/المؤسسة');
            $table->string('client_code')->comment('كود العميل الفريد');
            $table->string('gene_name')->comment('اسم الجين');
            $table->boolean('is_active')->default(true)->comment('هل الجين مفعل؟');
            $table->json('configuration')->nullable()->comment('إعدادات الجين');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->timestamps();
            
            // Indexes
            $table->unique(['client_code', 'gene_name']);
            $table->index('client_code');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_genes');
    }
};
