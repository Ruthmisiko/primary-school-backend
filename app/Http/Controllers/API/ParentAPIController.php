<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\Result;
use App\Models\Payment;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\AppBaseController;

/**
 * Class ParentAPIController
 */
class ParentAPIController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     * GET /parents
     */
    public function index(): JsonResponse
    {
        $parents = StudentParent::with(['student.sclass', 'school'])
                               ->where('school_id', auth()->user()->school_id)
                               ->get();

        return $this->sendResponse($parents->toArray(), 'Parents retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     * POST /parents
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();
        $input['school_id'] = auth()->user()->school_id;

        $parent = StudentParent::create($input);

        return $this->sendResponse($parent->toArray(), 'Parent saved successfully');
    }

    /**
     * Display the specified resource.
     * GET /parents/{id}
     */
    public function show($id): JsonResponse
    {
        $parent = StudentParent::with(['student.sclass', 'school'])
                              ->where('school_id', auth()->user()->school_id)
                              ->find($id);

        if (empty($parent)) {
            return $this->sendError('Parent not found');
        }

        return $this->sendResponse($parent->toArray(), 'Parent retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     * PUT/PATCH /parents/{id}
     */
    public function update($id, Request $request): JsonResponse
    {
        $parent = StudentParent::where('school_id', auth()->user()->school_id)->find($id);

        if (empty($parent)) {
            return $this->sendError('Parent not found');
        }

        $input = $request->all();
        $input['school_id'] = auth()->user()->school_id;

        $parent = $parent->update($input);

        return $this->sendResponse($parent, 'Parent updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /parents/{id}
     */
    public function destroy($id): JsonResponse
    {
        $parent = StudentParent::where('school_id', auth()->user()->school_id)->find($id);

        if (empty($parent)) {
            return $this->sendError('Parent not found');
        }

        $parent->delete();

        return $this->sendResponse($id, 'Parent deleted successfully');
    }

    /**
     * Get parent's children
     * GET /parent/children
     */
    public function getChildren(): JsonResponse
    {
        $parent = auth()->user();
        
        if ($parent->userType !== 'parent') {
            return $this->sendError('Unauthorized access');
        }

        $children = Student::whereHas('parents', function($query) use ($parent) {
            $query->where('phone_number', $parent->phone_number)
                  ->where('school_id', $parent->school_id);
        })->with(['sclass', 'school'])->get();

        // Format the response
        $formattedChildren = $children->map(function($child) {
            return [
                'id' => $child->id,
                'name' => $child->name,
                'class' => $child->sclass->name ?? 'N/A',
                'class_id' => $child->class_id,
                'age' => $child->age,
                'fee_balance' => $child->fee_balance,
                'paid_fee' => $child->paid_fee,
                'school' => $child->school->name ?? 'N/A',
                'created_at' => $child->created_at,
                'updated_at' => $child->updated_at,
            ];
        });

        return $this->sendResponse($formattedChildren->toArray(), 'Children retrieved successfully');
    }

    /**
     * Get parent's children results
     * GET /parent/children/results
     */
    public function getChildrenResults(Request $request): JsonResponse
    {
        $parent = auth()->user();
        
        if ($parent->userType !== 'parent') {
            return $this->sendError('Unauthorized access');
        }

        $studentId = $request->get('student_id');
        $term = $request->get('term');
        $year = $request->get('year');

        // Get parent's children first
        $children = Student::whereHas('parents', function($query) use ($parent) {
            $query->where('phone_number', $parent->phone_number)
                  ->where('school_id', $parent->school_id);
        })->pluck('id');

        if ($children->isEmpty()) {
            return $this->sendResponse([], 'No children found for this parent');
        }

        // Get results for parent's children
        $query = Result::whereIn('student_id', $children)
                      ->where('school_id', $parent->school_id)
                      ->with(['student.sclass', 'exam', 'subject']);

        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        if ($term) {
            $query->whereHas('exam', function($q) use ($term) {
                $q->where('term', $term);
            });
        }

        if ($year) {
            $query->whereHas('exam', function($q) use ($year) {
                $q->where('academic_year', $year);
            });
        }

        $results = $query->orderBy('created_at', 'desc')->get();

        // Format the response
        $formattedResults = $results->map(function($result) {
            return [
                'id' => $result->id,
                'student_id' => $result->student_id,
                'student_name' => $result->student->name ?? 'N/A',
                'student_class' => $result->student->sclass->name ?? 'N/A',
                'subject' => $result->subject->name ?? 'N/A',
                'exam' => $result->exam->name ?? 'N/A',
                'term' => $result->exam->term ?? 'N/A',
                'academic_year' => $result->exam->academic_year ?? 'N/A',
                'marks' => $result->marks,
                'total_marks' => $result->total_marks,
                'grade' => $result->grade,
                'remarks' => $result->remarks,
                'created_at' => $result->created_at,
                'updated_at' => $result->updated_at,
            ];
        });

        return $this->sendResponse($formattedResults->toArray(), 'Results retrieved successfully');
    }

    /**
     * Get parent's children payments
     * GET /parent/children/payments
     */
    public function getChildrenPayments(Request $request): JsonResponse
    {
        $parent = auth()->user();
        
        if ($parent->userType !== 'parent') {
            return $this->sendError('Unauthorized access');
        }

        $studentId = $request->get('student_id');
        $schoolId = $parent->school_id;

        // Get parent's children first
        $children = Student::whereHas('parents', function($query) use ($parent) {
            $query->where('phone_number', $parent->phone_number)
                  ->where('school_id', $parent->school_id);
        })->pluck('id');

        if ($children->isEmpty()) {
            return $this->sendResponse([], 'No children found for this parent');
        }

        // Get payments for parent's children
        $query = Payment::whereIn('student_id', $children)
                       ->where('school_id', $schoolId)
                       ->with(['student.sclass', 'student.school']);

        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        $payments = $query->orderBy('created_at', 'desc')->get();

        // Format the response
        $formattedPayments = $payments->map(function($payment) {
            return [
                'id' => $payment->id,
                'student_id' => $payment->student_id,
                'student_name' => $payment->student->name ?? 'N/A',
                'student_class' => $payment->student->sclass->name ?? 'N/A',
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'status' => $payment->status,
                'payment_method' => $payment->payment_method,
                'transaction_id' => $payment->transaction_id,
                'description' => $payment->description,
                'created_at' => $payment->created_at,
                'updated_at' => $payment->updated_at,
            ];
        });

        return $this->sendResponse($formattedPayments->toArray(), 'Payments retrieved successfully');
    }

    /**
     * Get parent dashboard data
     * GET /parent/dashboard
     */
    public function getDashboardData(): JsonResponse
    {
        $parent = auth()->user();
        
        if ($parent->userType !== 'parent') {
            return $this->sendError('Unauthorized access');
        }

        // Get parent's children
        $children = Student::whereHas('parents', function($query) use ($parent) {
            $query->where('phone_number', $parent->phone_number)
                  ->where('school_id', $parent->school_id);
        })->with(['sclass'])->get();

        $childrenCount = $children->count();
        $childrenIds = $children->pluck('id');

        // Calculate fees from student records
        $totalFees = $children->sum('fee_balance') + $children->sum('paid_fee');
        $paidAmount = $children->sum('paid_fee');
        $pendingAmount = $children->sum('fee_balance');

        // Get actual payment data from payments table
        $payments = Payment::whereIn('student_id', $childrenIds)
                          ->where('school_id', $parent->school_id)
                          ->get();

        $totalPaidFromPayments = $payments->where('status', 'COMPLETED')->sum('amount');
        $pendingPayments = $payments->where('status', 'PENDING')->sum('amount');

        // Get recent payments for activities
        $recentPayments = Payment::whereIn('student_id', $childrenIds)
                                ->where('school_id', $parent->school_id)
                                ->orderBy('created_at', 'desc')
                                ->limit(3)
                                ->get();

        $recentActivities = [];
        
        foreach ($recentPayments as $payment) {
            $student = $children->where('id', $payment->student_id)->first();
            $recentActivities[] = [
                'title' => 'Payment ' . ucfirst(strtolower($payment->status)),
                'description' => $student ? $student->name . ' - $' . $payment->amount : 'Payment - $' . $payment->amount,
                'time' => $payment->created_at->diffForHumans(),
                'icon' => $payment->status === 'COMPLETED' ? 'mdi-check-circle' : 'mdi-clock',
                'color' => $payment->status === 'COMPLETED' ? 'success' : 'warning'
            ];
        }

        // Add some default activities if no payments
        if (empty($recentActivities)) {
            $recentActivities = [
                [
                    'title' => 'Welcome to Parent Portal',
                    'description' => 'Access your child\'s academic information',
                    'time' => 'Just now',
                    'icon' => 'mdi-account-school',
                    'color' => 'info'
                ]
            ];
        }

        $dashboardData = [
            'children_count' => $childrenCount,
            'total_fees' => $totalFees,
            'paid_amount' => $totalPaidFromPayments > 0 ? $totalPaidFromPayments : $paidAmount,
            'pending_amount' => $pendingAmount,
            'recent_activities' => $recentActivities,
            'children' => $children->map(function($child) {
                return [
                    'id' => $child->id,
                    'name' => $child->name,
                    'class' => $child->sclass->name ?? 'N/A',
                    'fee_balance' => $child->fee_balance,
                    'paid_fee' => $child->paid_fee
                ];
            })
        ];

        return $this->sendResponse($dashboardData, 'Dashboard data retrieved successfully');
    }

    /**
     * Get upcoming events
     * GET /parent/events
     */
    public function getEvents(): JsonResponse
    {
        $parent = auth()->user();
        
        if ($parent->userType !== 'parent') {
            return $this->sendError('Unauthorized access');
        }

        // Get events for the parent's school
        $events = Event::where('school_id', $parent->school_id)
                      ->orderBy('date', 'asc')
                      ->get();

        // Format the events for the frontend
        $formattedEvents = $events->map(function($event) {
            return [
                'id' => $event->id,
                'tittle' => $event->tittle,
                'description' => $event->description,
                'date' => $event->date,
                'time' => $event->time ?? 'All Day',
                'location' => $event->location ?? 'School',
                'category' => $event->category ?? 'General',
                'status' => $event->status,
                'statusColor' => $this->getStatusColor($event->status),
                'color' => $this->getEventColor($event->status),
                'icon' => $this->getEventIcon($event->category ?? 'General'),
                'iconColor' => $this->getEventColor($event->status)
            ];
        });

        return $this->sendResponse($formattedEvents->toArray(), 'Events retrieved successfully');
    }

    /**
     * Get status color for events
     */
    private function getStatusColor($status)
    {
        switch (strtolower($status)) {
            case 'upcoming':
                return 'success';
            case 'ongoing':
                return 'primary';
            case 'completed':
                return 'info';
            case 'cancelled':
                return 'error';
            default:
                return 'grey';
        }
    }

    /**
     * Get event color based on status
     */
    private function getEventColor($status)
    {
        switch (strtolower($status)) {
            case 'upcoming':
                return 'primary';
            case 'ongoing':
                return 'success';
            case 'completed':
                return 'info';
            case 'cancelled':
                return 'error';
            default:
                return 'grey';
        }
    }

    /**
     * Get event icon based on category
     */
    private function getEventIcon($category)
    {
        switch (strtolower($category)) {
            case 'parent-teacher':
                return 'mdi-account-group';
            case 'sports':
                return 'mdi-trophy';
            case 'academic':
                return 'mdi-book';
            case 'cultural':
                return 'mdi-music';
            case 'exam':
                return 'mdi-file-document';
            case 'holiday':
                return 'mdi-palm-tree';
            default:
                return 'mdi-calendar';
        }
    }
}