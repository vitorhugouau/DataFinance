<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $fillable = [
        'title',
        'description',
        'target_amount',
        'current_amount',
        'due_date',
        'priority',
        'status',
        'category',
        'color',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    protected $appends = [
        'progress',
        'remaining_amount',
        'is_completed',
    ];

    public function getProgressAttribute(): float
    {
        if ($this->target_amount <= 0) {
            return 0;
        }

        return round(min(100, ($this->current_amount / $this->target_amount) * 100), 2);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) ($this->target_amount - $this->current_amount));
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed' || $this->current_amount >= $this->target_amount;
    }
}
