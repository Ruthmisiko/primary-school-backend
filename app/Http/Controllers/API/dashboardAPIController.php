<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Supplier;
use App\Models\Expense;
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
        $totalSuppliers = Supplier::count();
        $totalExpenses = Expense::sum('amount');
        // $totalAmount = Payment::sum('amount');

        $responseData = [
            // 'dashboards' => $dashboards->toArray(),
            'total_students' => $totalStudents,
            'total_teachers' => $totalTeachers,
            'total_users' => $totalUsers,
            'total_suppliers' => $totalSuppliers,
            'total_expenses' => $totalExpenses,
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

    /**
     * Get monthly enrollment statistics
     * GET /dashboards/enrollment-stats
     */
    public function getEnrollmentStats(Request $request): JsonResponse
    {
        $year = $request->get('year', date('Y'));
        
        // Get monthly enrollment data
        $monthlyEnrollments = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        for ($month = 1; $month <= 12; $month++) {
            $count = Student::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();
            
            $monthlyEnrollments[] = [
                'month' => $months[$month - 1],
                'count' => $count,
                'month_number' => $month
            ];
        }

        // Get total for the year
        $totalForYear = Student::whereYear('created_at', $year)->count();
        
        // Get comparison with previous year
        $previousYearTotal = Student::whereYear('created_at', $year - 1)->count();
        $percentageChange = $previousYearTotal > 0 
            ? round((($totalForYear - $previousYearTotal) / $previousYearTotal) * 100, 2) 
            : 0;

        return $this->sendResponse([
            'monthly_data' => $monthlyEnrollments,
            'total_for_year' => $totalForYear,
            'previous_year_total' => $previousYearTotal,
            'percentage_change' => $percentageChange,
            'year' => $year
        ], 'Enrollment statistics retrieved successfully');
    }

    /**
     * Get monthly expense statistics
     * GET /dashboards/expense-stats
     */
    public function getExpenseStats(Request $request): JsonResponse
    {
        $year = $request->get('year', date('Y'));
        
        // Get monthly expense data
        $monthlyExpenses = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        for ($month = 1; $month <= 12; $month++) {
            $total = Expense::whereYear('expense_date', $year)
                ->whereMonth('expense_date', $month)
                ->sum('amount');
            
            $monthlyExpenses[] = [
                'month' => $months[$month - 1],
                'amount' => (float) $total,
                'month_number' => $month
            ];
        }

        // Get total for the year
        $totalForYear = Expense::whereYear('expense_date', $year)->sum('amount');

        return $this->sendResponse([
            'monthly_data' => $monthlyExpenses,
            'total_for_year' => $totalForYear,
            'year' => $year
        ], 'Expense statistics retrieved successfully');
    }
}
