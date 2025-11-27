<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function monthlyExpenses(Request $request): JsonResponse
    {
        $month = $request->input('month', date('Y-m'));
        $transactions = Transaction::where('type', 'expense')
            ->whereYear('date', substr($month, 0, 4))
            ->whereMonth('date', substr($month, 5, 2))
            ->sum('value');

        return response()->json(['total' => (float) $transactions, 'month' => $month]);
    }

    public function expensesByCategory(Request $request): JsonResponse
    {
        $month = $request->input('month', date('Y-m'));
        $data = Transaction::where('type', 'expense')
            ->whereYear('date', substr($month, 0, 4))
            ->whereMonth('date', substr($month, 5, 2))
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(function ($transactions, $categoryId) {
                $category = $transactions->first()->category;
                $total = $transactions->sum('value');

                return [
                    'category_id' => $categoryId,
                    'category_name' => $category ? $category->name : 'Sem categoria',
                    'category_color' => $category ? $category->color : '#6c757d',
                    'total' => (float) $total,
                ];
            })
            ->values();

        return response()->json($data);
    }

    public function monthlyEvolution(Request $request): JsonResponse
    {
        $months = Transaction::select(
            DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
            DB::raw('SUM(CASE WHEN type = "income" THEN value ELSE 0 END) as income'),
            DB::raw('SUM(CASE WHEN type = "expense" THEN value ELSE 0 END) as expense')
        )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'income' => (float) $item->income,
                    'expense' => (float) $item->expense,
                    'balance' => (float) ($item->income - $item->expense),
                ];
            });

        return response()->json($months);
    }

    public function totalAssets(): JsonResponse
    {
        $accounts = Account::where('active', true)->get();
        $total = $accounts->sum(function ($account) {
            return $account->current_balance;
        });

        $byType = $accounts->groupBy('type')->map(function ($accounts) {
            return $accounts->sum(function ($account) {
                return $account->current_balance;
            });
        });

        return response()->json([
            'total' => (float) $total,
            'by_type' => $byType,
        ]);
    }
}
