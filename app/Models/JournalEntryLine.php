<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="JournalEntryLine",
 *     title="Journal Entry Line",
 *     description="Represents a single line item within a Journal Entry, detailing the debit or credit to a specific account and cost center.",
 *     @OA\Property(property="id", type="integer", readOnly="true", description="The unique identifier for the journal entry line."),
 *     @OA\Property(property="journal_entry_id", type="integer", description="Foreign key to the JournalEntry model."),
 *     @OA\Property(property="account_id", type="integer", description="Foreign key to the Account model (Chart of Accounts)."),
 *     @OA\Property(property="cost_center_id", type="integer", nullable="true", description="Foreign key to the CostCenter model."),
 *     @OA\Property(property="debit", type="number", format="float", description="The debit amount for the line item."),
 *     @OA\Property(property="credit", type="number", format="float", description="The credit amount for the line item."),
 *     @OA\Property(property="description", type="string", nullable="true", description="A brief description or narration for the line item."),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly="true", description="Timestamp of creation."),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true", description="Timestamp of last update.")
 * )
 */
class JournalEntryLine extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'journal_entry_lines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'cost_center_id',
        'debit',
        'credit',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Casting financial amounts to 'decimal' for precision is a best practice.
        // Laravel's decimal cast handles string conversion for storage and float for retrieval.
        'debit' => 'decimal:4', // Assuming 4 decimal places for currency precision
        'credit' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // --- Relationships ---

    /**
     * Get the journal entry that owns the line item.
     *
     * @return BelongsTo
     */
    public function journalEntry(): BelongsTo
    {
        // Assumes a JournalEntry model exists in the App\Models namespace
        return $this->belongsTo(JournalEntry::class);
    }

    /**
     * Get the account (from the Chart of Accounts) associated with the line item.
     *
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        // Assumes an Account model exists in the App\Models namespace
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the cost center associated with the line item for departmental tracking.
     *
     * @return BelongsTo
     */
    public function costCenter(): BelongsTo
    {
        // Assumes a CostCenter model exists in the App\Models namespace
        // cost_center_id is often nullable in financial systems
        return $this->belongsTo(CostCenter::class);
    }

    // --- Custom Methods (Example) ---

    /**
     * Check if the line item is a debit entry.
     *
     * @return bool
     */
    public function isDebit(): bool
    {
        // Using a small epsilon for float comparison safety, though decimal cast helps
        return $this->debit > 0.0000;
    }

    /**
     * Check if the line item is a credit entry.
     *
     * @return bool
     */
    public function isCredit(): bool
    {
        return $this->credit > 0.0000;
    }
}
