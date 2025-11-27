<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property string $account_number
 * @property string $name_ar
 * @property string $name_en
 * @property int $account_type_id
 * @property int|null $parent_id
 * @property bool $is_analytical
 * @property string|null $analytical_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\AccountType $accountType
 * @property-read \App\Models\Account|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Account> $children
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\JournalEntryLine> $journalEntryLines
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereIsAnalytical(bool $value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account root()
 * @method static \Illuminate\Database\Eloquent\Builder|Account childrenOf(int $parentId)
 */
class Account extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_number',
        'name_ar',
        'name_en',
        'account_type_id',
        'parent_id',
        'is_analytical',
        'analytical_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_analytical' => 'boolean',
    ];

    // ========================================================================
    // RELATIONSHIPS
    // ========================================================================

    /**
     * Get the account type that owns the Account.
     * (e.g., Asset, Liability, Equity, Revenue, Expense)
     *
     * @return BelongsTo
     */
    public function accountType(): BelongsTo
    {
        // Assuming an AccountType model exists
        return $this->belongsTo(AccountType::class);
    }

    /**
     * Get the parent account for the current account (tree structure).
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    /**
     * Get the child accounts for the current account (tree structure).
     *
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    /**
     * Get the journal entry lines associated with the account.
     *
     * @return HasMany
     */
    public function journalEntryLines(): HasMany
    {
        // Assuming a JournalEntryLine model exists
        return $this->hasMany(JournalEntryLine::class);
    }

    // ========================================================================
    // TREE STRUCTURE METHODS
    // ========================================================================

    /**
     * Check if the current account is a root account (has no parent).
     *
     * @return bool
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Check if the current account is a leaf account (has no children).
     *
     * @return bool
     */
    public function isLeaf(): bool
    {
        return $this->children()->doesntExist();
    }

    /**
     * Get all ancestors (parents) of the current account.
     *
     * @return \Illuminate\Database\Eloquent\Collection<Account>
     */
    public function ancestors()
    {
        $ancestors = collect();
        $parent = $this->parent;

        while ($parent) {
            $ancestors->push($parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    /**
     * Get all descendants (children and their children) of the current account.
     *
     * @return \Illuminate\Database\Eloquent\Collection<Account>
     */
    public function descendants()
    {
        $descendants = collect();
        $children = $this->children;

        foreach ($children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->descendants());
        }

        return $descendants;
    }

    // ========================================================================
    // SCOPES
    // ========================================================================

    /**
     * Scope a query to only include root accounts (accounts with no parent).
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope a query to only include children of a specific parent ID.
     *
     * @param Builder $query
     * @param int $parentId
     * @return Builder
     */
    public function scopeChildrenOf(Builder $query, int $parentId): Builder
    {
        return $query->where('parent_id', $parentId);
    }

    /**
     * Scope a query to only include analytical accounts.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeAnalytical(Builder $query): Builder
    {
        return $query->where('is_analytical', true);
    }

    // ========================================================================
    // BUSINESS LOGIC
    // ========================================================================

    /**
     * Determine if the account is a sub-account (i.e., not a root account).
     *
     * @return bool
     */
    public function isSubAccount(): bool
    {
        return ! $this->isRoot();
    }

    /**
     * Get the full account number path (e.g., 1000.10.01).
     * NOTE: This assumes the account_number field is the segment number.
     *
     * @return string
     */
    public function getFullAccountNumberAttribute(): string
    {
        $numbers = $this->ancestors()->reverse()->pluck('account_number')->toArray();
        $numbers[] = $this->account_number;

        return implode('.', $numbers);
    }

    /**
     * Get the balance of the account (simplified example).
     * In a real system, this would involve complex aggregation from journal entries.
     *
     * @return float
     */
    public function getBalance(): float
    {
        // Placeholder for complex accounting logic.
        // In a real scenario, this would sum up debit/credit from journalEntryLines
        // and apply the account type's normal balance (Debit/Credit).
        return (float) $this->journalEntryLines()->sum('amount');
    }
}