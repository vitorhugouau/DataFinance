<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGoalRequest;
use App\Http\Requests\UpdateGoalRequest;
use App\Models\Goal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index(): JsonResponse
    {
        $goals = Goal::orderBy('due_date')->get();

        return response()->json($goals);
    }

    public function store(StoreGoalRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $payload['current_amount'] = $payload['current_amount'] ?? 0;
        $goal = Goal::create($payload);

        return response()->json($goal, 201);
    }

    public function show(Goal $goal): JsonResponse
    {
        return response()->json($goal);
    }

    public function update(UpdateGoalRequest $request, Goal $goal): JsonResponse
    {
        $goal->update($request->validated());

        return response()->json($goal);
    }

    public function destroy(Goal $goal): JsonResponse
    {
        $goal->delete();

        return response()->json(null, 204);
    }

    public function updateProgress(Request $request, Goal $goal): JsonResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'operation' => ['nullable', 'string', 'in:add,set'],
        ]);

        if (($data['operation'] ?? 'add') === 'set') {
            $goal->current_amount = $data['amount'];
        } else {
            $goal->current_amount += $data['amount'];
        }

        if ($goal->current_amount >= $goal->target_amount) {
            $goal->status = 'completed';
        }

        $goal->save();

        return response()->json($goal->refresh());
    }
}
