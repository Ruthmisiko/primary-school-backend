<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateParentAPIRequest;
use App\Http\Requests\API\UpdateParentAPIRequest;
use App\Models\StudentParent;
use App\Repositories\ParentRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class ParentAPIController
 */
class ParentAPIController extends AppBaseController
{
    private ParentRepository $parentRepository;

    public function __construct(ParentRepository $parentRepo)
    {
        $this->parentRepository = $parentRepo;
    }

    /**
     * Display a listing of the Parents.
     * GET|HEAD /parents
     */
    public function index(Request $request): JsonResponse
    {
        // Load parents with student relationship (bypass global scopes)
        $parents = StudentParent::with(['student' => function($query) {
            $query->withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)
                ->with(['sclass' => function($subQuery) {
                    $subQuery->withoutGlobalScope(\App\Models\Scopes\SchoolScope::class);
                }]);
        }])->get();

        return $this->sendResponse($parents->toArray(), 'Parents retrieved successfully');
    }

    /**
     * Store a newly created Parent in storage.
     * POST /parents
     */
    public function store(CreateParentAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $parent = $this->parentRepository->create($input);

        return $this->sendResponse($parent->toArray(), 'Parent saved successfully');
    }

    /**
     * Display the specified Parent.
     * GET|HEAD /parents/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Parent $parent */
        $parent = $this->parentRepository->find($id);

        if (empty($parent)) {
            return $this->sendError('Parent not found');
        }

        return $this->sendResponse($parent->toArray(), 'Parent retrieved successfully');
    }

    /**
     * Update the specified Parent in storage.
     * PUT/PATCH /parents/{id}
     */
    public function update($id, Request $request): JsonResponse
    {
        // Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'student_id' => 'required|exists:students,id',
            'phone_number' => 'required|string|max:20',
            'id_number' => 'nullable|string|max:50',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:255',
        ]);

        $parent = StudentParent::find($id);

        if (empty($parent)) {
            return $this->sendError('Parent not found');
        }

        $validated['school_id'] = auth()->user()->school_id;
        $parent->update($validated);

        // Reload with relationships
        $parent = StudentParent::with(['student.sclass'])->find($id);

        return $this->sendResponse($parent->toArray(), 'Parent updated successfully');
    }

    /**
     * Remove the specified Parent from storage.
     * DELETE /parents/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Parent $parent */
        $parent = $this->parentRepository->find($id);

        if (empty($parent)) {
            return $this->sendError('Parent not found');
        }

        $parent->delete();

        return $this->sendSuccess('Parent deleted successfully');
    }
}
