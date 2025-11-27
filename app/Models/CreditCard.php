<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreditCard extends Model
{
    protected $fillable = [
        'account_id',
        'name',
        'last_four_digits',
        'limit',
        'current_balance',
        'closing_date',
        'due_date',
        'active',
    ];

    protected $casts = [
        'limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'closing_date' => 'date',
        'due_date' => 'date',
        'active' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(CreditCardExpense::class);
    }

    public function getAvailableLimitAttribute(): float
    {
        return (float) ($this->limit - $this->current_balance);
    }

    public function getUsagePercentageAttribute(): float
    {
        if ($this->limit == 0) {
            return 0;
        }

        return (float) (($this->current_balance / $this->limit) * 100);
    }
}
