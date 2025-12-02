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
        Schema::dropIfExists('alabasi_partners');
        Schema::create('alabasi_partners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('code')->unique()->nullable();

            // البنية الخماسية (The Quintet Structure)
            $table->unsignedBigInteger('holding_id');
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('project_id');

            // حقول التدقيق (Auditing Fields)
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Soft Deletes and Timestamps
            $table->timestamps();
            $table->softDeletes();

            // الفهارس (Indexes)
            $table->index(['holding_id', 'unit_id', 'project_id'], 'partners_quintet_index');
            $table->index('created_by');
            $table->index('updated_by');

            // Foreign Keys (افتراض وجود الجداول المرجعية)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alabasi_partners');
    }
};
