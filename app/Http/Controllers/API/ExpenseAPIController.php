<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateExpenseAPIRequest;
use App\Http\Requests\API\UpdateExpenseAPIRequest;
use App\Models\Expense;
use App\Repositories\ExpenseRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class ExpenseAPIController
 */
class ExpenseAPIController extends AppBaseController
{
    private ExpenseRepository $expenseRepository;

    public function __construct(ExpenseRepository $expenseRepo)
    {
        $this->expenseRepository = $expenseRepo;
    }

    /**
     * Display a listing of the Expenses.
     * GET|HEAD /expenses
     */
    public function index(Request $request): JsonResponse
    {
        $expenses = $this->expenseRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($expenses->toArray(), 'Expenses retrieved successfully');
    }

    /**
     * Store a newly created Expense in storage.
     * POST /expenses
     */
    public function store(CreateExpenseAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $input['school_id'] = auth()->user()->school_id;

        $expense = $this->expenseRepository->create($input);

        return $this->sendResponse($expense->toArray(), 'Expense saved successfully');
    }

    /**
     * Display the specified Expense.
     * GET|HEAD /expenses/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Expense $expense */
        $expense = $this->expenseRepository->find($id);

        if (empty($expense)) {
            return $this->sendError('Expense not found');
        }

        return $this->sendResponse($expense->toArray(), 'Expense retrieved successfully');
    }

    /**
     * Update the specified Expense in storage.
     * PUT/PATCH /expenses/{id}
     */
    public function update($id, UpdateExpenseAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Expense $expense */
        $expense = $this->expenseRepository->find($id);

        if (empty($expense)) {
            return $this->sendError('Expense not found');
        }

        $expense = $this->expenseRepository->update($input, $id);

        return $this->sendResponse($expense->toArray(), 'Expense updated successfully');
    }

    /**
     * Remove the specified Expense from storage.
     * DELETE /expenses/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Expense $expense */
        $expense = $this->expenseRepository->find($id);

        if (empty($expense)) {
            return $this->sendError('Expense not found');
        }

        $expense->delete();

        return $this->sendSuccess('Expense deleted successfully');
    }
}
