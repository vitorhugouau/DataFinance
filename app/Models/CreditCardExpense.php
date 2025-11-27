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
        'total_value',
        'installments',
        'current_installment',
        'date',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'total_value' => 'decimal:2',
        'installments' => 'integer',
        'current_installment' => 'integer',
        'date' => 'date',
    ];

    public function creditCard(): BelongsTo
    {
        return $this->belongsTo(CreditCard::class);
    }
}
