<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = ['name', 'type', 'currency', 'initial_balance', 'active'];

    protected $casts = [
        'active' => 'boolean',
        'initial_balance' => 'decimal:2',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getCurrentBalanceAttribute(): float
    {
        $income = $this->transactions()
            ->where('type', 'income')
            ->sum('value');

        $expense = $this->transactions()
            ->where('type', 'expense')
            ->sum('value');

        return (float) $this->initial_balance + $income - $expense;
    }
}
