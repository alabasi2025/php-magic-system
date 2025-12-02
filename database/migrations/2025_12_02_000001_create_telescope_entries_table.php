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
        Schema::create('telescope_entries', function (Blueprint $table) {
            $table->bigIncrements('sequence');
            $table->uuid('uuid')->unique();
            $table->uuid('batch_id');
            $table->string('family_hash')->nullable();
            $table->boolean('should_display_on_index')->default(true);
            $table->string('type', 20);
            $table->longText('content');
            $table->dateTime('created_at')->nullable();

            $table->index('batch_id');
            $table->index('created_at');
            $table->index('type');
            $table->index('family_hash');
        });

        Schema::create('telescope_entries_tags', function (Blueprint $table) {
            $table->id();
            $table->uuid('entry_uuid');
            $table->string('tag');

            $table->unique(['entry_uuid', 'tag']);
            $table->foreign('entry_uuid')->references('uuid')->on('telescope_entries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telescope_entries_tags');
        Schema::dropIfExists('telescope_entries');
    }
};
