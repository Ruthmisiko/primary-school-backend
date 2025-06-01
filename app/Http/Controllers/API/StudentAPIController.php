<?php

namespace App\Http\Controllers\API;

use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Imports\StudentsImport;
use Illuminate\Http\JsonResponse;
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
        $students = $this->studentRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        $students = Student::with(['sclass', 'results'])->get();

        return $this->sendResponse($students->toArray(), 'Students retrieved successfully');
    }

    /**
     * Store a newly created Student in storage.
     * POST /students
     */
    public function store(CreateStudentAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $student = $this->studentRepository->create($input);

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
        $student = Student::with(['sclass', 'results', 'results.exam', 'results.subject'])->find($id);

        return $this->sendResponse($student->toArray(), 'Student retrieved successfully');
    }

    /**
     * Update the specified Student in storage.
     * PUT/PATCH /students/{id}
     */
    public function update($id, UpdateStudentAPIRequest $request): JsonResponse
    {
        $input = $request->all();

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

}
