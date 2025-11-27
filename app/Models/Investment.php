<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Investment extends Model
{
    protected $fillable = [
        'account_id',
        'type',
        'symbol',
        'name',
        'amount',
        'purchase_price',
        'purchase_date',
        'current_price',
        'interest_rate',
        'interest_type',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'purchase_price' => 'decimal:2',
        'current_price' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'purchase_date' => 'date',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function getTotalInvestedAttribute(): float
    {
        return (float) ($this->amount * $this->purchase_price);
    }

    public function getCurrentValueAttribute(): float
    {
        if ($this->current_price) {
            return (float) ($this->amount * $this->current_price);
        }

        return $this->total_invested;
    }

    public function getProfitAttribute(): float
    {
        return $this->current_value - $this->total_invested;
    }

    public function getProfitPercentageAttribute(): float
    {
        if ($this->total_invested == 0) {
            return 0;
        }

        return (float) (($this->profit / $this->total_invested) * 100);
    }

    public function getInterestEarnedAttribute(): float
    {
        if (! $this->interest_rate || ! $this->interest_type) {
            return 0;
        }

        $daysSincePurchase = now()->diffInDays($this->purchase_date);
        $rate = (float) $this->interest_rate;

        if ($this->interest_type === 'yearly') {
            return (float) ($this->total_invested * ($rate / 100) * ($daysSincePurchase / 365));
        } elseif ($this->interest_type === 'monthly') {
            return (float) ($this->total_invested * ($rate / 100) * ($daysSincePurchase / 30));
        }

        return 0;
    }
}
