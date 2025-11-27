<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditCardExpense extends Model
{
    protected $fillable = [
        'credit_card_id',
        'name',
        'value',
        'date',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'date' => 'date',
    ];

    public function creditCard(): BelongsTo
    {
        return $this->belongsTo(CreditCard::class);
    }
}
