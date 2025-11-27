<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvestmentRequest;
use App\Models\Investment;
use Illuminate\Http\JsonResponse;

class InvestmentController extends Controller
{
    public function index(): JsonResponse
    {
        $investments = Investment::with('account')->get()->map(function ($investment) {
            $investment->total_invested = $investment->total_invested;
            $investment->current_value = $investment->current_value;
            $investment->profit = $investment->profit;
            $investment->profit_percentage = $investment->profit_percentage;
            $investment->interest_earned = $investment->interest_earned;

            return $investment;
        });

        return response()->json($investments);
    }

    public function show(Investment $investment): JsonResponse
    {
        $investment->load('account');
        $investment->total_invested = $investment->total_invested;
        $investment->current_value = $investment->current_value;
        $investment->profit = $investment->profit;
        $investment->profit_percentage = $investment->profit_percentage;
        $investment->interest_earned = $investment->interest_earned;

        return response()->json($investment);
    }

    public function store(StoreInvestmentRequest $request): JsonResponse
    {
        $investment = Investment::create($request->validated());
        $investment->load('account');

        return response()->json($investment, 201);
    }

    public function update(StoreInvestmentRequest $request, Investment $investment): JsonResponse
    {
        $investment->update($request->validated());
        $investment->refresh();
        $investment->load('account');

        return response()->json($investment);
    }

    public function destroy(Investment $investment): JsonResponse
    {
        $investment->delete();

        return response()->json(null, 204);
    }
}
