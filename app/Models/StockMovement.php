<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $warehouse_id The ID of the warehouse where the movement occurred.
 * @property string $movement_type The type of stock movement (e.g., 'in', 'out', 'transfer').
 * @property string $reference_number A unique reference number for the movement (e.g., invoice number, transfer order).
 * @property string|null $notes Any additional notes regarding the movement.
 * @property \Illuminate\Support\Carbon $movement_date The date the stock movement took place.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Warehouse $warehouse The warehouse associated with this stock movement.
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\StockMovementLine[] $lines The lines (items) associated with this stock movement.
 */
class StockMovement extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stock_movements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'warehouse_id',
        'movement_type',
        'reference_number',
        'notes',
        'movement_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'movement_date' => 'date',
    ];

    /**
     * Get the warehouse that owns the StockMovement.
     *
     * @return BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        // Assumes a 'Warehouse' model exists and the foreign key is 'warehouse_id'
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    /**
     * Get the stock movement lines for the StockMovement.
     *
     * @return HasMany
     */
    public function lines(): HasMany
    {
        // Assumes a 'StockMovementLine' model exists and the foreign key is 'stock_movement_id'
        return $this->hasMany(StockMovementLine::class, 'stock_movement_id');
    }

    /**
     * Get the validation rules for the model attributes.
     * This method can be used for post-creation validation (e.g., in a Request class or Controller).
     *
     * @param self|null $instance The model instance for update scenarios.
     * @return array<string, array<int, string>>
     */
    public static function rules(?self $instance = null): array
    {
        return [
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'movement_type' => ['required', 'string', 'in:in,out,transfer,adjustment'], // Example types
            'reference_number' => [
                'required',
                'string',
                'max:255',
                // Ensure reference_number is unique per movement_type, ignoring the current record on update
                'unique:stock_movements,reference_number,' . ($instance ? $instance->id : 'NULL') . ',id,movement_type,' . ($instance ? $instance->movement_type : 'NULL'),
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
            'movement_date' => ['required', 'date', 'before_or_equal:today'],
            // Validation for nested lines can be added here if using a single request for both header and lines
            // e.g., 'lines' => ['required', 'array', 'min:1'],
            // 'lines.*.item_id' => ['required', 'integer', 'exists:items,id'],
            // 'lines.*.quantity' => ['required', 'numeric', 'min:0.0001'],
        ];
    }
}