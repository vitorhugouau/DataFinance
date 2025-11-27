<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receivable extends Model
{
    protected $fillable = [
        'debtor_name',
        'description',
        'amount',
        'due_date',
        'paid_date',
        'paid',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
        'paid' => 'boolean',
    ];

    public function isOverdue(): bool
    {
        return ! $this->paid && $this->due_date < now();
    }

    public function getDaysOverdueAttribute(): int
    {
        if (! $this->isOverdue()) {
            return 0;
        }

        return now()->diffInDays($this->due_date);
    }
}
