<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Receivable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReceivableController extends Controller
{
    public function index(): JsonResponse
    {
        $receivables = Receivable::orderBy('due_date')->get()->map(function ($receivable) {
            $receivable->is_overdue = $receivable->isOverdue();
            $receivable->days_overdue = $receivable->days_overdue;

            return $receivable;
        });

        return response()->json($receivables);
    }

    public function show(Receivable $receivable): JsonResponse
    {
        $receivable->is_overdue = $receivable->isOverdue();
        $receivable->days_overdue = $receivable->days_overdue;

        return response()->json($receivable);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'debtor_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'due_date' => ['required', 'date'],
            'paid' => ['nullable', 'boolean'],
            'paid_date' => ['nullable', 'date'],
        ]);

        $receivable = Receivable::create($validated);

        return response()->json($receivable, 201);
    }

    public function update(Request $request, Receivable $receivable): JsonResponse
    {
        $validated = $request->validate([
            'debtor_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['nullable', 'numeric', 'min:0.01'],
            'due_date' => ['nullable', 'date'],
            'paid' => ['nullable', 'boolean'],
            'paid_date' => ['nullable', 'date'],
        ]);

        $receivable->update($validated);
        $receivable->refresh();
        $receivable->is_overdue = $receivable->isOverdue();
        $receivable->days_overdue = $receivable->days_overdue;

        return response()->json($receivable);
    }

    public function destroy(Receivable $receivable): JsonResponse
    {
        $receivable->delete();

        return response()->json(null, 204);
    }

    public function markAsPaid(Receivable $receivable): JsonResponse
    {
        $receivable->paid = true;
        $receivable->paid_date = now();
        $receivable->save();

        return response()->json($receivable);
    }
}
