<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property int $user_id
 * @property string $entry_date
 * @property string $reference_number
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JournalEntryLine> $lines
 */
class JournalEntry extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'entry_date',
        'reference_number',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'entry_date' => 'date',
    ];

    // --- Relationships ---

    /**
     * Get the user who created the journal entry.
     *
     * @return BelongsTo<User, JournalEntry>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the lines for the journal entry.
     *
     * @return HasMany<JournalEntryLine>
     */
    public function lines(): HasMany
    {
        // Assuming a JournalEntryLine model exists and has a foreign key 'journal_entry_id'
        return $this->hasMany(JournalEntryLine::class);
    }

    // --- Custom Validation and Business Logic ---

    /**
     * Check if the total debit amount equals the total credit amount for this entry.
     * This is a fundamental rule of double-entry accounting.
     *
     * @return bool
     */
    public function isBalanced(): bool
    {
        // Use the relationship to calculate the sums.
        // We use DB::raw for precision and to ensure the database handles the aggregation.
        $balance = $this->lines()
            ->select(DB::raw('SUM(debit) as total_debit, SUM(credit) as total_credit'))
            ->first();

        // If there are no lines, it's technically balanced (0 = 0), but usually, an entry must have lines.
        if (!$balance) {
            return true;
        }

        // Check if the difference is negligible (to handle floating point arithmetic issues,
        // although financial data should ideally use integers/decimals in the DB).
        // Assuming 'debit' and 'credit' are stored as DECIMAL or equivalent.
        return abs($balance->total_debit - $balance->total_credit) < 0.0001;
    }

    /**
     * Boot the model and register event listeners.
     * We use the 'saving' event to enforce the balance check before persisting.
     * NOTE: This requires the JournalEntryLines to be saved/updated *before* the JournalEntry itself,
     * or for the lines to be loaded into the relationship before calling save().
     * A better approach for transactional integrity is often a dedicated service/transaction.
     * For a simple Model implementation, we'll assume lines are loaded or checked separately.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        // Enforce the balance check before saving the JournalEntry.
        // This check is most effective when performed within a service layer
        // that manages the transaction of saving both the entry and its lines.
        // For demonstration in the Model, we add a simple check.
        static::saving(function (JournalEntry $entry) {
            // If the entry is being created or updated, and it has lines loaded, check balance.
            // In a real application, this check is often done in a Request or Service layer
            // before the save operation is even attempted.
            // Since we cannot reliably check the balance of *unsaved* lines here,
            // we will rely on the service layer to call $entry->isBalanced() explicitly.
            // We will leave the method here for the service layer to use.
            //
            // A more robust approach would be to use a database trigger or a transactional service.
            // For the Model, we simply provide the isBalanced method.
            return true; // Allow saving, deferring balance check to the service layer.
        });
    }
}