<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class FixedExpense extends Model
{
    protected $fillable = [
        'account_id',
        'category_id',
        'name',
        'description',
        'amount',
        'due_date',
        'frequency',
        'currency',
        'auto_debit',
        'active',
        'last_paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'last_paid_at' => 'date',
        'auto_debit' => 'boolean',
        'active' => 'boolean',
    ];

    protected $appends = [
        'is_overdue',
        'days_until_due',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getIsOverdueAttribute(): bool
    {
        if (! $this->due_date instanceof Carbon) {
            return false;
        }

        return $this->active && $this->due_date->isPast();
    }

    public function getDaysUntilDueAttribute(): int
    {
        if (! $this->due_date instanceof Carbon) {
            return 0;
        }

        return now()->diffInDays($this->due_date, false);
    }
}
