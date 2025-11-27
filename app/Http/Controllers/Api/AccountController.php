<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccountRequest;
use App\Models\Account;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    public function index(): JsonResponse
    {
        $accounts = Account::all()->map(function ($account) {
            $account->current_balance = $account->current_balance;

            return $account;
        });

        return response()->json($accounts);
    }

    public function show(Account $account): JsonResponse
    {
        $account->current_balance = $account->current_balance;

        return response()->json($account);
    }

    public function store(StoreAccountRequest $request): JsonResponse
    {
        $account = Account::create($request->validated());

        return response()->json($account, 201);
    }

    public function update(StoreAccountRequest $request, Account $account): JsonResponse
    {
        $account->update($request->validated());
        $account->refresh();
        $account->current_balance = $account->current_balance;

        return response()->json($account);
    }

    public function destroy(Account $account): JsonResponse
    {
        $account->delete();

        return response()->json(null, 204);
    }
}
