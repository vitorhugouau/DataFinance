<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CreditCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreditCardController extends Controller
{
    public function index(): JsonResponse
    {
        $cards = CreditCard::with(['account', 'expenses'])->get()->map(function ($card) {
            $card->available_limit = $card->available_limit;
            $card->usage_percentage = $card->usage_percentage;

            return $card;
        });

        return response()->json($cards);
    }

    public function show(CreditCard $creditCard): JsonResponse
    {
        $creditCard->load(['account', 'expenses']);
        $creditCard->available_limit = $creditCard->available_limit;
        $creditCard->usage_percentage = $creditCard->usage_percentage;

        return response()->json($creditCard);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'account_id' => ['required', 'exists:accounts,id'],
            'name' => ['required', 'string', 'max:255'],
            'last_four_digits' => ['required', 'string', 'size:4'],
            'limit' => ['required', 'numeric', 'min:0'],
            'current_balance' => ['nullable', 'numeric', 'min:0'],
            'closing_date' => ['required', 'date'],
            'due_date' => ['required', 'date'],
            'active' => ['nullable', 'boolean'],
        ]);

        $creditCard = CreditCard::create($validated);
        $creditCard->load('account');

        return response()->json($creditCard, 201);
    }

    public function update(Request $request, CreditCard $creditCard): JsonResponse
    {
        $validated = $request->validate([
            'account_id' => ['nullable', 'exists:accounts,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'last_four_digits' => ['nullable', 'string', 'size:4'],
            'limit' => ['nullable', 'numeric', 'min:0'],
            'current_balance' => ['nullable', 'numeric', 'min:0'],
            'closing_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'active' => ['nullable', 'boolean'],
        ]);

        $creditCard->update($validated);
        $creditCard->refresh();
        $creditCard->load('account');
        $creditCard->available_limit = $creditCard->available_limit;
        $creditCard->usage_percentage = $creditCard->usage_percentage;

        return response()->json($creditCard);
    }

    public function destroy(CreditCard $creditCard): JsonResponse
    {
        $creditCard->delete();

        return response()->json(null, 204);
    }
}
