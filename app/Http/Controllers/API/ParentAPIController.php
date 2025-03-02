<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateParentAPIRequest;
use App\Http\Requests\API\UpdateParentAPIRequest;
use App\Models\Parent;
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
        $parents = $this->parentRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

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
    public function update($id, UpdateParentAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Parent $parent */
        $parent = $this->parentRepository->find($id);

        if (empty($parent)) {
            return $this->sendError('Parent not found');
        }

        $parent = $this->parentRepository->update($input, $id);

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
