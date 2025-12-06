<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * StockMovement Model
 * 
 * Represents all stock movements (in, out, transfer, adjustment, return).
 * 
 * @property int $id
 * @property string $movement_number Unique movement reference number
 * @property string $movement_type stock_in|stock_out|transfer|adjustment|return
 * @property int $warehouse_id Source/destination warehouse
 * @property int|null $to_warehouse_id Destination warehouse (for transfers)
 * @property int $item_id Item being moved
 * @property float $quantity Quantity moved (positive or negative for adjustments)
 * @property float $unit_cost Cost per unit at time of movement
 * @property float $total_cost Total cost (quantity * unit_cost)
 * @property string $movement_date Date of movement
 * @property int|null $reference_id Reference to related document (PO, SO, etc.)
 * @property string|null $reference_type Type of reference document
 * @property int|null $approved_by User who approved the movement
 * @property string|null $approved_at Approval timestamp
 * @property string $status pending|approved|rejected
 * @property string|null $notes
 * @property int|null $journal_entry_id Related accounting journal entry
 * @property int $created_by User who created the movement
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class StockMovement extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'movement_number',
        'movement_type',
        'warehouse_id',
        'to_warehouse_id',
        'item_id',
        'quantity',
        'unit_cost',
        'total_cost',
        'movement_date',
        'reference_id',
        'reference_type',
        'approved_by',
        'approved_at',
        'status',
        'notes',
        'journal_entry_id',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'movement_date' => 'date',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Boot method to auto-calculate total_cost.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($movement) {
            $movement->total_cost = $movement->quantity * $movement->unit_cost;
        });
    }

    /**
     * Get the warehouse for this movement.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    /**
     * Get the destination warehouse (for transfers).
     */
    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    /**
     * Get the item for this movement.
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * Get the user who approved this movement.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who created this movement.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the related journal entry.
     */
    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    /**
     * Scope to get pending movements.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get approved movements.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get rejected movements.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope to filter by movement type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('movement_type', $type);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('movement_date', [$startDate, $endDate]);
    }

    /**
     * Check if movement is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if movement is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if movement is a transfer.
     */
    public function isTransfer(): bool
    {
        return $this->movement_type === 'transfer';
    }

    /**
     * Get movement type label in Arabic.
     */
    public function getMovementTypeLabel(): string
    {
        $labels = [
            'stock_in' => 'إدخال بضاعة',
            'stock_out' => 'إخراج بضاعة',
            'transfer' => 'نقل بين المخازن',
            'adjustment' => 'تسوية مخزون',
            'return' => 'إرجاع بضاعة',
        ];

        return $labels[$this->movement_type] ?? $this->movement_type;
    }

    /**
     * Get status label in Arabic.
     */
    public function getStatusLabel(): string
    {
        $labels = [
            'pending' => 'قيد الانتظار',
            'approved' => 'معتمد',
            'rejected' => 'مرفوض',
        ];

        return $labels[$this->status] ?? $this->status;
    }
}
