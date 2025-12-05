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
        Schema::table('journal_entries', function (Blueprint $table) {
            // Add missing fields
            $table->string('reference', 100)->nullable()->after('description');
            $table->text('notes')->nullable()->after('is_balanced');
            
            // Add user tracking fields
            $table->unsignedBigInteger('created_by')->nullable()->after('notes');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            $table->unsignedBigInteger('approved_by')->nullable()->after('updated_by');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            
            // Add soft deletes
            $table->softDeletes()->after('updated_at');
            
            // Add indexes
            $table->index('entry_number');
            $table->index('entry_date');
            $table->index('status');
            $table->index('created_by');
            $table->index('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropIndex(['entry_number']);
            $table->dropIndex(['entry_date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['approved_by']);
            
            $table->dropSoftDeletes();
            $table->dropColumn([
                'reference',
                'notes',
                'created_by',
                'updated_by',
                'approved_by',
                'approved_at',
            ]);
        });
    }
};
