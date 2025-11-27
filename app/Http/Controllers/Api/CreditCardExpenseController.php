<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CreditCard;
use App\Models\CreditCardExpense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreditCardExpenseController extends Controller
{
    public function index(CreditCard $creditCard): JsonResponse
    {
        $expenses = $creditCard->expenses()->orderBy('date', 'desc')->get();

        return response()->json($expenses);
    }

    public function store(Request $request, CreditCard $creditCard): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'value' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
        ]);

        $expense = $creditCard->expenses()->create($validated);

        // Update credit card balance
        $creditCard->current_balance += $expense->value;
        $creditCard->save();

        return response()->json($expense, 201);
    }

    public function destroy(Request $request, CreditCard $creditCard, int $expense): JsonResponse
    {
        $expenseModel = CreditCardExpense::findOrFail($expense);

        // Verify expense belongs to credit card
        if ($expenseModel->credit_card_id != $creditCard->id) {
            return response()->json(['message' => 'Gasto não pertence a este cartão'], 403);
        }

        // Update credit card balance before deleting
        $creditCard->current_balance -= $expenseModel->value;
        if ($creditCard->current_balance < 0) {
            $creditCard->current_balance = 0;
        }
        $creditCard->save();

        $expenseModel->delete();

        return response()->json(null, 204);
    }
}
