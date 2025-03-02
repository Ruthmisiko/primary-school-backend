<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateResultAPIRequest;
use App\Http\Requests\API\UpdateResultAPIRequest;
use App\Models\Result;
use App\Repositories\ResultRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class ResultAPIController
 */
class ResultAPIController extends AppBaseController
{
    private ResultRepository $resultRepository;

    public function __construct(ResultRepository $resultRepo)
    {
        $this->resultRepository = $resultRepo;
    }

    /**
     * Display a listing of the Results.
     * GET|HEAD /results
     */
    public function index(Request $request): JsonResponse
    {
        $results = $this->resultRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($results->toArray(), 'Results retrieved successfully');
    }

    /**
     * Store a newly created Result in storage.
     * POST /results
     */
    public function store(CreateResultAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $result = $this->resultRepository->create($input);

        return $this->sendResponse($result->toArray(), 'Result saved successfully');
    }

    /**
     * Display the specified Result.
     * GET|HEAD /results/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Result $result */
        $result = $this->resultRepository->find($id);

        if (empty($result)) {
            return $this->sendError('Result not found');
        }

        return $this->sendResponse($result->toArray(), 'Result retrieved successfully');
    }

    /**
     * Update the specified Result in storage.
     * PUT/PATCH /results/{id}
     */
    public function update($id, UpdateResultAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Result $result */
        $result = $this->resultRepository->find($id);

        if (empty($result)) {
            return $this->sendError('Result not found');
        }

        $result = $this->resultRepository->update($input, $id);

        return $this->sendResponse($result->toArray(), 'Result updated successfully');
    }

    /**
     * Remove the specified Result from storage.
     * DELETE /results/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Result $result */
        $result = $this->resultRepository->find($id);

        if (empty($result)) {
            return $this->sendError('Result not found');
        }

        $result->delete();

        return $this->sendSuccess('Result deleted successfully');
    }
}
