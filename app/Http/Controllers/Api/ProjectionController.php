<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Category;
use App\Models\CreditCard;
use App\Models\Investment;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectionController extends Controller
{
    public function calculate(Request $request): JsonResponse
    {
        $selectedExpenses = $request->input('expenses', []);
        $customOperations = $request->input('custom_operations', []);
        if (! is_array($customOperations)) {
            $customOperations = [];
        }
        $month = $request->input('month', date('Y-m'));
        $options = $request->input('options', []);

        // Get current balances from all active accounts
        $accounts = Account::where('active', true)->get();
        $totalAvailable = $accounts->sum(function ($account) {
            return $account->current_balance;
        });

        // Get expected income for the month
        $expectedIncome = Transaction::where('type', 'income')
            ->whereYear('date', substr($month, 0, 4))
            ->whereMonth('date', substr($month, 5, 2))
            ->sum('value');

        // Calculate total selected expenses
        $totalExpenses = 0;
        $expensesDetails = [];

        foreach ($selectedExpenses as $expense) {
            $category = Category::find($expense['category_id'] ?? null);
            $amount = (float) ($expense['amount'] ?? 0);
            $totalExpenses += $amount;

            $expensesDetails[] = [
                'category' => $category ? $category->name : 'Sem categoria',
                'amount' => $amount,
                'description' => $expense['description'] ?? '',
            ];
        }

        // Get credit cards balance
        $creditCardsBalance = CreditCard::where('active', true)->sum('current_balance');

        // Get investments total (always calculate, even if not subtracting)
        $investments = Investment::all();
        $investmentsTotal = $investments->sum(function ($investment) {
            return $investment->total_invested;
        });

        // Get account balance (if specific account selected)
        $accountBalance = 0;
        if (in_array('use_account_balance', $options) && $request->has('account_id')) {
            $account = Account::find($request->input('account_id'));
            if ($account) {
                $accountBalance = $account->current_balance;
                $totalAvailable = $accountBalance; // Override total available
            }
        }

        // Calculate base projection
        $baseProjection = $totalAvailable + $expectedIncome - $totalExpenses;

        // Apply subtractions based on options
        $projectedBalance = $baseProjection;
        $creditCardSubtractionSource = $request->input('credit_card_subtraction_source', 'projection'); // 'projection', 'investment', 'account'
        $finalInvestmentsTotal = $investmentsTotal;

        if (in_array('subtract_credit_cards', $options)) {
            if ($creditCardSubtractionSource === 'investment') {
                // Subtrair do investimento
                $finalInvestmentsTotal -= $creditCardsBalance;
                if ($finalInvestmentsTotal < 0) {
                    $finalInvestmentsTotal = 0;
                }
            } elseif ($creditCardSubtractionSource === 'account') {
                // Subtrair do saldo da conta
                $totalAvailable -= $creditCardsBalance;
                if ($totalAvailable < 0) {
                    $totalAvailable = 0;
                }
                $baseProjection = $totalAvailable + $expectedIncome - $totalExpenses;
                $projectedBalance = $baseProjection;
            } else {
                // Subtrair da projeção (padrão)
                $projectedBalance -= $creditCardsBalance;
            }
        }

        if (in_array('subtract_investments', $options)) {
            $projectedBalance -= $finalInvestmentsTotal;
        }

        $baseMetrics = [
            'current_balance' => (float) $totalAvailable,
            'expected_income' => (float) $expectedIncome,
            'total_expenses' => (float) $totalExpenses,
            'credit_cards_balance' => (float) $creditCardsBalance,
            'investments_total' => (float) $finalInvestmentsTotal,
            'projected_balance' => (float) $projectedBalance,
        ];
        $customMetricValues = $baseMetrics;
        $customResults = [];

        foreach ($customOperations as $operation) {
            $applyTo = $operation['apply_to'] ?? null;
            $operationType = $operation['operation'] ?? 'subtract';
            $valueKey = $operation['value'] ?? null;
            $valueKeys = $operation['values'] ?? [];
            if (empty($valueKeys) && $valueKey) {
                $valueKeys = [$valueKey];
            }
            $valueKeys = array_values(array_filter($valueKeys, fn ($key) => is_string($key)));

            if (! isset($customMetricValues[$applyTo]) || empty($valueKeys)) {
                continue;
            }

            $validValueKeys = array_filter($valueKeys, fn ($key) => isset($customMetricValues[$key]));

            if (empty($validValueKeys)) {
                continue;
            }

            $baseValue = $customMetricValues[$applyTo];
            $valueAmount = array_reduce($validValueKeys, function ($carry, $key) use ($customMetricValues) {
                return $carry + ($customMetricValues[$key] ?? 0);
            }, 0);
            $result = $operationType === 'add'
                ? $baseValue + $valueAmount
                : $baseValue - $valueAmount;

            $customMetricValues[$applyTo] = $result;

            $customResults[] = [
                'apply_to' => $applyTo,
                'value_key' => $validValueKeys[0] ?? null,
                'value_keys' => array_values($validValueKeys),
                'operation' => $operationType,
                'result' => (float) $result,
                'base_value' => (float) $baseValue,
                'value_amount' => (float) $valueAmount,
            ];
        }

        return response()->json([
            'current_balance' => (float) $totalAvailable,
            'expected_income' => (float) $expectedIncome,
            'total_expenses' => (float) $totalExpenses,
            'credit_cards_balance' => (float) $creditCardsBalance,
            'investments_total' => (float) $finalInvestmentsTotal,
            'projected_balance' => (float) $projectedBalance,
            'expenses_details' => $expensesDetails,
            'month' => $month,
            'options' => $options,
            'custom_results' => $customResults,
        ]);
    }

    public function categories(): JsonResponse
    {
        $categories = Category::where('type', 'expense')
            ->with('children')
            ->get();

        return response()->json($categories);
    }

    public function accounts(): JsonResponse
    {
        $accounts = Account::where('active', true)->get()->map(function ($account) {
            $account->current_balance = $account->current_balance;

            return $account;
        });

        return response()->json($accounts);
    }
}
