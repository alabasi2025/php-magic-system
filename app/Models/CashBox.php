<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\OptimizedQueries;
// use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * CashBox Model
 * 
 * @package App\Models
 * @property int $id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class CashBox extends Model
{
    use HasFactory, OptimizedQueries;

    /**
     * Default relations to eager load
     * 
     * @var array
     */
    protected $defaultRelations = ['unit', 'intermediateAccount'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cash_boxes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'balance',
        'is_active',
        'unit_id',
        'intermediate_account_id',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * Get the attributes that should be searchable.
     *
     * @return array<int, string>
     */
    public function getSearchableAttributes(): array
    {
        return ['name', 'description', 'code'];
    }

    /**
     * Get the unit that owns the cash box.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the intermediate account associated with the cash box.
     */
    public function intermediateAccount()
    {
        return $this->belongsTo(ChartAccount::class, 'intermediate_account_id');
    }

    /**
     * Get the user who created the cash box.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the cash box.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get all receipts for this cash box.
     */
    public function receipts()
    {
        return $this->morphMany(CashReceipt::class, 'account');
    }

    /**
     * Get all payments for this cash box.
     */
    public function payments()
    {
        return $this->morphMany(CashPayment::class, 'account');
    }
}
