<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSclassAPIRequest;
use App\Http\Requests\API\UpdateSclassAPIRequest;
use App\Models\Sclass;
use App\Repositories\SclassRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class SclassAPIController
 */
class SclassAPIController extends AppBaseController
{
    private SclassRepository $sclassRepository;

    public function __construct(SclassRepository $sclassRepo)
    {
        $this->sclassRepository = $sclassRepo;
    }

    /**
     * Display a listing of the Sclasses.
     * GET|HEAD /sclasses
     */
    public function index(Request $request): JsonResponse
    {
        $sclasses = $this->sclassRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );
        $sclasses = Sclass::with(['teacher'])->get();

        return $this->sendResponse($sclasses->toArray(), 'Sclasses retrieved successfully');
    }

    /**
     * Store a newly created Sclass in storage.
     * POST /sclasses
     */
    public function store(CreateSclassAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $sclass = $this->sclassRepository->create($input);

        return $this->sendResponse($sclass->toArray(), 'Sclass saved successfully');
    }

    /**
     * Display the specified Sclass.
     * GET|HEAD /sclasses/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Sclass $sclass */
        $sclass = $this->sclassRepository->find($id);

        if (empty($sclass)) {
            return $this->sendError('Sclass not found');
        }
        $sclass = Sclass::with(['teacher'])->find($id);

        return $this->sendResponse($sclass->toArray(), 'Sclass retrieved successfully');
    }

    /**
     * Update the specified Sclass in storage.
     * PUT/PATCH /sclasses/{id}
     */
    public function update($id, UpdateSclassAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Sclass $sclass */
        $sclass = $this->sclassRepository->find($id);

        if (empty($sclass)) {
            return $this->sendError('Sclass not found');
        }

        $sclass = $this->sclassRepository->update($input, $id);

        return $this->sendResponse($sclass->toArray(), 'Sclass updated successfully');
    }

    /**
     * Remove the specified Sclass from storage.
     * DELETE /sclasses/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Sclass $sclass */
        $sclass = $this->sclassRepository->find($id);

        if (empty($sclass)) {
            return $this->sendError('Sclass not found');
        }

        $sclass->delete();

        return $this->sendSuccess('Sclass deleted successfully');
    }
}
