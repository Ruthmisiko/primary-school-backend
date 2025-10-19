<?php

namespace App\Http\Controllers\API;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use App\Imports\StudentsImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use App\Models\StudentParent;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentTemplateExport;
use App\Repositories\StudentRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateStudentAPIRequest;
use App\Http\Requests\API\UpdateStudentAPIRequest;

/**
 * Class StudentAPIController
 */
class StudentAPIController extends AppBaseController
{
    private StudentRepository $studentRepository;

    public function __construct(StudentRepository $studentRepo)
    {
        $this->studentRepository = $studentRepo;
    }

    /**
     * Display a listing of the Students.
     * GET|HEAD /students
     */
    public function index(Request $request): JsonResponse
    {
        $query = Student::with(['sclass', 'results', 'school']);

        // Apply filters
        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        // Search by name
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where('name', 'like', "%{$searchTerm}%");
        }

        // Order by
        $orderBy = $request->get('orderBy', 'created_at');
        $sortedBy = $request->get('sortedBy', 'desc');
        $query->orderBy($orderBy, $sortedBy);

        $students = $query->get();

        return $this->sendResponse($students->toArray(), 'Students retrieved successfully');
    }

    /**
     * Store a newly created Student in storage.
     * POST /students
     */
    public function store(CreateStudentAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $input['school_id'] = auth()->user()->school_id;

        $student = $this->studentRepository->create($input);

        if ($request->has('parent') && $request->has('phone_number')) {
            // Create parent record
            $parent = StudentParent::create([
                'name'        => $request->input('parent'),
                'phone_number'=> $request->input('phone_number'),
                'address'=> $request->input('address'),
                'student_id'  => $student->id,
                'school_id'   => $input['school_id'],
            ]);

            // Create parent user account
            $parentEmail = strtolower(str_replace(' ', '', $request->input('parent'))) . '@gmail.com';
            $parentPassword = $request->input('phone_number'); // Use phone number as password
            
            // Check if parent user already exists
            $existingParentUser = User::where('email', $parentEmail)->first();
            
            if (!$existingParentUser) {
                User::create([
                    'name' => $request->input('parent'),
                    'username' => $request->input('parent'), // Set username to parent name
                    'email' => $parentEmail,
                    'password' => bcrypt($parentPassword),
                    'userType' => 'parent',
                    'phone_number' => $request->input('phone_number'),
                    'school_id' => $input['school_id'],
                ]);
            }
        }

        return $this->sendResponse($student->toArray(), 'Student saved successfully');
    }

    /**
     * Display the specified Student.
     * GET|HEAD /students/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Student $student */
        $student = $this->studentRepository->find($id);

        if (empty($student)) {
            return $this->sendError('Student not found');
        }
        $student = Student::with(['sclass', 'results', 'results.exam', 'results.subject','school'])->find($id);

        return $this->sendResponse($student->toArray(), 'Student retrieved successfully');
    }

    /**
     * Update the specified Student in storage.
     * PUT/PATCH /students/{id}
     */
    public function update($id, UpdateStudentAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $input['school_id'] = auth()->user()->school_id;


        /** @var Student $student */
        $student = $this->studentRepository->find($id);

        if (empty($student)) {
            return $this->sendError('Student not found');
        }

        $student = $this->studentRepository->update($input, $id);

        return $this->sendResponse($student->toArray(), 'Student updated successfully');
    }

    /**
     * Remove the specified Student from storage.
     * DELETE /students/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Student $student */
        $student = $this->studentRepository->find($id);

        if (empty($student)) {
            return $this->sendError('Student not found');
        }

        $student->delete();

        return $this->sendSuccess('Student deleted successfully');
    }

    public function ImportStudents(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv|max:2048',
        ]);

        Excel::import(new StudentsImport, $request->file('file'));

        return response()->json(['message' => 'Students imported successfully'], 200);
    }

    public function downloadTemplate()
    {
        return Excel::download(new StudentTemplateExport(), 'student_upload_template.xlsx');
    }

    public function printResult($id)
    {
        $pdf = PDF::loadView('results.print', ['student' => Student::findOrFail($id)]);
        return $pdf->download("student-result-{$id}.pdf");
    }
    public function StudentsReportPdf (): \Illuminate\Http\Response
    {

        $students = Student::with(['sclass'])->get();

        return Pdf::loadView('reports.students-report', ['students' => $students])
            ->setPaper('a4', 'landscape')
            ->download('students-report.pdf');
    }
}
