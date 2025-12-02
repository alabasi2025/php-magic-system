<?php

namespace App\Genes\PARTNERSHIP_ACCOUNTING\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Genes\PARTNERSHIP_ACCOUNTING\Models\PartnershipAccount;
use App\Genes\PARTNERSHIP_ACCOUNTING\Models\Transaction;

class Partner extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'partners';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'partner_type',
        'contact_email',
        'contact_phone',
        'address',
        'partnership_account_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // --- Relations ---

    /**
     * Get the partnership account that owns the Partner.
     * (BelongsTo)
     */
    public function partnershipAccount()
    {
        return $this->belongsTo(PartnershipAccount::class);
    }

    /**
     * Get the transactions for the Partner.
     * (HasMany)
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // --- Scopes ---

    /**
     * Scope a query to only include individual partners.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeIndividual(Builder $query)
    {
        $query->where('partner_type', 'individual');
    }

    /**
     * Scope a query to only include company partners.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeCompany(Builder $query)
    {
        $query->where('partner_type', 'company');
    }

    // --- Accessors ---

    /**
     * Get the partner's name formatted in uppercase.
     *
     * @return string
     */
    protected function getFormattedNameAttribute(): string
    {
        return strtoupper($this->name);
    }

    /**
     * Determine if the partner is an individual.
     *
     * @return bool
     */
    protected function getIsIndividualAttribute(): bool
    {
        return $this->partner_type === 'individual';
    }
}
