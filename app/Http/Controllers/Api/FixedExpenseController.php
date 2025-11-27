<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFixedExpenseRequest;
use App\Http\Requests\UpdateFixedExpenseRequest;
use App\Models\FixedExpense;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class FixedExpenseController extends Controller
{
    public function index(): JsonResponse
    {
        $expenses = FixedExpense::with(['account', 'category'])
            ->orderBy('due_date')
            ->get();

        return response()->json($expenses);
    }

    public function store(StoreFixedExpenseRequest $request): JsonResponse
    {
        $expense = FixedExpense::create($request->validated());
        $expense->load(['account', 'category']);

        return response()->json($expense, 201);
    }

    public function show(FixedExpense $fixedExpense): JsonResponse
    {
        $fixedExpense->load(['account', 'category']);

        return response()->json($fixedExpense);
    }

    public function update(UpdateFixedExpenseRequest $request, FixedExpense $fixedExpense): JsonResponse
    {
        $fixedExpense->update($request->validated());
        $fixedExpense->load(['account', 'category']);

        return response()->json($fixedExpense);
    }

    public function destroy(FixedExpense $fixedExpense): JsonResponse
    {
        $fixedExpense->delete();

        return response()->json(null, 204);
    }

    public function markAsPaid(FixedExpense $fixedExpense): JsonResponse
    {
        $fixedExpense->last_paid_at = now();
        if ($fixedExpense->due_date) {
            $fixedExpense->due_date = $this->calculateNextDueDate($fixedExpense);
        }
        $fixedExpense->save();
        $fixedExpense->load(['account', 'category']);

        return response()->json($fixedExpense);
    }

    private function calculateNextDueDate(FixedExpense $fixedExpense): Carbon
    {
        $currentDueDate = $fixedExpense->due_date ?? now();

        return match ($fixedExpense->frequency) {
            'weekly' => $currentDueDate->copy()->addWeek(),
            'biweekly' => $currentDueDate->copy()->addWeeks(2),
            'quarterly' => $currentDueDate->copy()->addMonths(3),
            'yearly' => $currentDueDate->copy()->addYear(),
            default => $currentDueDate->copy()->addMonth(),
        };
    }
}
