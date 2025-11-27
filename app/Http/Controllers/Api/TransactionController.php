<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Transaction::with(['account', 'category', 'relatedAccount'])
                ->orderBy('date', 'desc')
                ->get()
        );
    }

    public function show(Transaction $transaction): JsonResponse
    {
        return response()->json($transaction->load(['account', 'category', 'relatedAccount']));
    }

    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if ($validated['type'] === 'transfer') {
            $out = Transaction::create([
                'account_id' => $validated['account_id'],
                'category_id' => null,
                'type' => 'expense',
                'value' => $validated['value'],
                'date' => $validated['date'],
                'description' => $validated['description'] ?? null,
                'related_account_id' => $validated['related_account_id'],
            ]);

            $in = Transaction::create([
                'account_id' => $validated['related_account_id'],
                'category_id' => null,
                'type' => 'income',
                'value' => $validated['value'],
                'date' => $validated['date'],
                'description' => $validated['description'] ?? null,
                'related_account_id' => $validated['account_id'],
            ]);

            return response()->json([$out->load(['account', 'category', 'relatedAccount']), $in->load(['account', 'category', 'relatedAccount'])], 201);
        }

        $transaction = Transaction::create($validated);

        return response()->json($transaction->load(['account', 'category', 'relatedAccount']), 201);
    }

    public function update(StoreTransactionRequest $request, Transaction $transaction): JsonResponse
    {
        $transaction->update($request->validated());

        return response()->json($transaction->load(['account', 'category', 'relatedAccount']));
    }

    public function destroy(Transaction $transaction): JsonResponse
    {
        $transaction->delete();

        return response()->json(null, 204);
    }
}
