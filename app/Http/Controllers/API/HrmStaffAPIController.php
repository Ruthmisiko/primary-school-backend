<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHrmStaffAPIRequest;
use App\Http\Requests\API\UpdateHrmStaffAPIRequest;
use App\Models\HrmStaff;
use App\Repositories\HrmStaffRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class HrmStaffAPIController
 */
class HrmStaffAPIController extends AppBaseController
{
    private HrmStaffRepository $hrmStaffRepository;

    public function __construct(HrmStaffRepository $hrmStaffRepo)
    {
        $this->hrmStaffRepository = $hrmStaffRepo;
    }

    /**
     * Display a listing of the HrmStaffs.
     * GET|HEAD /hrm-staffs
     */
    public function index(Request $request): JsonResponse
    {
        $hrmStaffs = $this->hrmStaffRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($hrmStaffs->toArray(), 'Hrm Staffs retrieved successfully');
    }

    /**
     * Store a newly created HrmStaff in storage.
     * POST /hrm-staffs
     */
    public function store(CreateHrmStaffAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $hrmStaff = $this->hrmStaffRepository->create($input);

        return $this->sendResponse($hrmStaff->toArray(), 'Hrm Staff saved successfully');
    }

    /**
     * Display the specified HrmStaff.
     * GET|HEAD /hrm-staffs/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var HrmStaff $hrmStaff */
        $hrmStaff = $this->hrmStaffRepository->find($id);

        if (empty($hrmStaff)) {
            return $this->sendError('Hrm Staff not found');
        }

        return $this->sendResponse($hrmStaff->toArray(), 'Hrm Staff retrieved successfully');
    }

    /**
     * Update the specified HrmStaff in storage.
     * PUT/PATCH /hrm-staffs/{id}
     */
    public function update($id, UpdateHrmStaffAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var HrmStaff $hrmStaff */
        $hrmStaff = $this->hrmStaffRepository->find($id);

        if (empty($hrmStaff)) {
            return $this->sendError('Hrm Staff not found');
        }

        $hrmStaff = $this->hrmStaffRepository->update($input, $id);

        return $this->sendResponse($hrmStaff->toArray(), 'HrmStaff updated successfully');
    }

    /**
     * Remove the specified HrmStaff from storage.
     * DELETE /hrm-staffs/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var HrmStaff $hrmStaff */
        $hrmStaff = $this->hrmStaffRepository->find($id);

        if (empty($hrmStaff)) {
            return $this->sendError('Hrm Staff not found');
        }

        $hrmStaff->delete();

        return $this->sendSuccess('Hrm Staff deleted successfully');
    }
}
