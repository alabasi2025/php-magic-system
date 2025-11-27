<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property bool $is_closed
 * @property \Illuminate\Support\Carbon|null $closed_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\FiscalPeriod[] $periods
 */
class FiscalYear extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fiscal_years';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_closed',
        'closed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_closed' => 'boolean',
        'closed_at' => 'datetime',
    ];

    // --- Relationships ---

    /**
     * Get the fiscal periods for the fiscal year.
     *
     * @return HasMany
     */
    public function periods(): HasMany
    {
        // Assuming a FiscalPeriod model exists and has a foreign key 'fiscal_year_id'
        return $this->hasMany(FiscalPeriod::class, 'fiscal_year_id');
    }

    // --- Custom Methods ---

    /**
     * Closes the fiscal year.
     *
     * This method performs a transaction to ensure atomicity:
     * 1. Checks if the year is already closed.
     * 2. Closes all associated fiscal periods.
     * 3. Updates the fiscal year's status to closed.
     *
     * @return bool True if the year was successfully closed, false otherwise.
     * @throws \Exception If any period fails to close or the transaction fails.
     */
    public function close(): bool
    {
        if ($this->is_closed) {
            // Already closed, no action needed
            return true;
        }

        return DB::transaction(function () {
            // 1. Close all associated periods
            $this->periods->each(function ($period) {
                // Assuming FiscalPeriod model has a close() method
                // We assume FiscalPeriod::close() throws an exception on failure
                if (!$period->close()) {
                    throw new \Exception("Failed to close fiscal period ID: {$period->id}");
                }
            });

            // 2. Update the fiscal year's status
            $this->is_closed = true;
            $this->closed_at = now();

            if (!$this->save()) {
                throw new \Exception("Failed to save fiscal year ID: {$this->id} as closed.");
            }

            return true;
        });
    }

    /**
     * Opens the fiscal year.
     *
     * This method is typically used to reverse a closure, though in many
     * accounting systems, re-opening a closed year is restricted.
     * It performs a transaction to ensure atomicity.
     *
     * @return bool True if the year was successfully opened, false otherwise.
     * @throws \Exception If the transaction fails.
     */
    public function open(): bool
    {
        if (!$this->is_closed) {
            // Already open, no action needed
            return true;
        }

        return DB::transaction(function () {
            // 1. Open all associated periods (optional, depending on business logic)
            // For simplicity, we only open the year itself, assuming period opening
            // is handled separately or not required for re-opening.
            // If period opening is required, the following block should be uncommented
            // and adjusted based on the FiscalPeriod model's open() method.
            /*
            $this->periods->each(function ($period) {
                // Assuming FiscalPeriod model has an open() method
                if (!$period->open()) {
                    throw new \Exception("Failed to open fiscal period ID: {$period->id}");
                }
            });
            */

            // 2. Update the fiscal year's status
            $this->is_closed = false;
            $this->closed_at = null; // Clear the closed_at timestamp

            if (!$this->save()) {
                throw new \Exception("Failed to save fiscal year ID: {$this->id} as open.");
            }

            return true;
        });
    }

    // --- Scopes ---

    /**
     * Scope a query to only include open fiscal years.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOpen($query)
    {
        return $query->where('is_closed', false);
    }

    /**
     * Scope a query to only include closed fiscal years.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeClosed($query)
    {
        return $query->where('is_closed', true);
    }

    /**
     * Get the currently active fiscal year.
     *
     * @return FiscalYear|null
     */
    public static function getActiveYear(): ?FiscalYear
    {
        // This implementation assumes the latest open year is the active one.
        // In a real-world scenario, more complex logic (e.g., checking if
        // current date falls within the year's date range) might be needed.
        return static::open()
            ->orderByDesc('start_date')
            ->first();
    }
}