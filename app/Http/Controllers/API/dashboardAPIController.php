<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\dashboard;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\dashboardRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreatedashboardAPIRequest;
use App\Http\Requests\API\UpdatedashboardAPIRequest;

/**
 * Class dashboardAPIController
 */
class dashboardAPIController extends AppBaseController
{
    private dashboardRepository $dashboardRepository;

    public function __construct(dashboardRepository $dashboardRepo)
    {
        $this->dashboardRepository = $dashboardRepo;
    }

    /**
     * Display a listing of the dashboards.
     * GET|HEAD /dashboards
     */
    public function index(Request $request): JsonResponse
    {
        // $dashboards = $this->dashboardRepository->all(
        //     $request->except(['skip', 'limit']),
        //     $request->get('skip'),
        //     $request->get('limit')
        // );

        $totalStudents = Student::count();
        $totalUsers = User::count();
        $totalTeachers = Teacher::count();
        // $totalAmount = Payment::sum('amount');

        $responseData = [
            // 'dashboards' => $dashboards->toArray(),
            'total_students' => $totalStudents,
            'total_teachers' => $totalTeachers,
            'total_users' => $totalUsers,
            // 'total_amount' => $totalAmount,
        ];

        return $this->sendResponse($responseData, 'Data retrieved successfully');
    }

    /**
     * Store a newly created dashboard in storage.
     * POST /dashboards
     */
    public function store(CreatedashboardAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $dashboard = $this->dashboardRepository->create($input);

        return $this->sendResponse($dashboard->toArray(), 'Dashboard saved successfully');
    }

    /**
     * Display the specified dashboard.
     * GET|HEAD /dashboards/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var dashboard $dashboard */
        $dashboard = $this->dashboardRepository->find($id);

        if (empty($dashboard)) {
            return $this->sendError('Dashboard not found');
        }

        return $this->sendResponse($dashboard->toArray(), 'Dashboard retrieved successfully');
    }

    /**
     * Update the specified dashboard in storage.
     * PUT/PATCH /dashboards/{id}
     */
    public function update($id, UpdatedashboardAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var dashboard $dashboard */
        $dashboard = $this->dashboardRepository->find($id);

        if (empty($dashboard)) {
            return $this->sendError('Dashboard not found');
        }

        $dashboard = $this->dashboardRepository->update($input, $id);

        return $this->sendResponse($dashboard->toArray(), 'dashboard updated successfully');
    }

    /**
     * Remove the specified dashboard from storage.
     * DELETE /dashboards/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var dashboard $dashboard */
        $dashboard = $this->dashboardRepository->find($id);

        if (empty($dashboard)) {
            return $this->sendError('Dashboard not found');
        }

        $dashboard->delete();

        return $this->sendSuccess('Dashboard deleted successfully');
    }
}
