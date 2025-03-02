<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCocurricularAPIRequest;
use App\Http\Requests\API\UpdateCocurricularAPIRequest;
use App\Models\Cocurricular;
use App\Repositories\CocurricularRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class CocurricularAPIController
 */
class CocurricularAPIController extends AppBaseController
{
    private CocurricularRepository $cocurricularRepository;

    public function __construct(CocurricularRepository $cocurricularRepo)
    {
        $this->cocurricularRepository = $cocurricularRepo;
    }

    /**
     * Display a listing of the Cocurriculars.
     * GET|HEAD /cocurriculars
     */
    public function index(Request $request): JsonResponse
    {
        $cocurriculars = $this->cocurricularRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($cocurriculars->toArray(), 'Cocurriculars retrieved successfully');
    }

    /**
     * Store a newly created Cocurricular in storage.
     * POST /cocurriculars
     */
    public function store(CreateCocurricularAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $cocurricular = $this->cocurricularRepository->create($input);

        return $this->sendResponse($cocurricular->toArray(), 'Cocurricular saved successfully');
    }

    /**
     * Display the specified Cocurricular.
     * GET|HEAD /cocurriculars/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Cocurricular $cocurricular */
        $cocurricular = $this->cocurricularRepository->find($id);

        if (empty($cocurricular)) {
            return $this->sendError('Cocurricular not found');
        }

        return $this->sendResponse($cocurricular->toArray(), 'Cocurricular retrieved successfully');
    }

    /**
     * Update the specified Cocurricular in storage.
     * PUT/PATCH /cocurriculars/{id}
     */
    public function update($id, UpdateCocurricularAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Cocurricular $cocurricular */
        $cocurricular = $this->cocurricularRepository->find($id);

        if (empty($cocurricular)) {
            return $this->sendError('Cocurricular not found');
        }

        $cocurricular = $this->cocurricularRepository->update($input, $id);

        return $this->sendResponse($cocurricular->toArray(), 'Cocurricular updated successfully');
    }

    /**
     * Remove the specified Cocurricular from storage.
     * DELETE /cocurriculars/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Cocurricular $cocurricular */
        $cocurricular = $this->cocurricularRepository->find($id);

        if (empty($cocurricular)) {
            return $this->sendError('Cocurricular not found');
        }

        $cocurricular->delete();

        return $this->sendSuccess('Cocurricular deleted successfully');
    }
}
