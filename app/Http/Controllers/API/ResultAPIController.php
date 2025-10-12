<?php

namespace App\Http\Controllers\API;

use App\Models\Result;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Imports\ResultsImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ResultsTemplateExport;
use App\Repositories\ResultRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateResultAPIRequest;
use App\Http\Requests\API\UpdateResultAPIRequest;


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
        $query = Result::with(['sclass', 'exam', 'student', 'subject']);

        // Apply filters
        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('subject_id') && $request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('exam_id') && $request->exam_id) {
            $query->where('exam_id', $request->exam_id);
        }

        if ($request->has('student_id') && $request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->whereHas('student', function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%");
            });
        }

        // Order by
        $orderBy = $request->get('orderBy', 'created_at');
        $sortedBy = $request->get('sortedBy', 'desc');
        $query->orderBy($orderBy, $sortedBy);

        $results = $query->get();

        return $this->sendResponse($results->toArray(), 'Results retrieved successfully');
    }

    /**
     * Store a newly created Result in storage.
     * POST /results
     */

    public function store(CreateResultAPIRequest $request): JsonResponse
{
    $input = $request->only(['class_id', 'subject_id', 'exam_id']);

    $input['school_id'] = auth()->user()->school_id;

    $items = $request->input('result_items', []);

    $savedResults = [];

    foreach ($items as $item) {
        $data = array_merge($input, [
            'student_id' => $item['student_id'],
            'marks_obtained' => $item['marks_obtained'] ?? null,
            'remarks' => $item['remarks'] ?? null,
            'grade' => $item['grade'] ?? null,
        ]);

        $savedResults[] = $this->resultRepository->create($data);
    }

    return $this->sendResponse($savedResults, 'Results saved successfully');
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

        $input['school_id'] = auth()->user()->school_id;


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

    public function downloadTemplate()
    {
        return Excel::download(new ResultsTemplateExport(), 'result_upload_template.xlsx');
    }

    public function ImportResults(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv|max:2048',
        ]);

        try {
            $import = new ResultsImport();
            Excel::import($import, $request->file('file'));

            $processedCount = $import->getProcessedCount();
            $errorCount = $import->getErrorCount();

            \Log::info("Results import completed. Processed: {$processedCount}, Errors: {$errorCount}");

            return response()->json([
                'success' => true,
                'message' => 'Results imported successfully',
                'processed' => $processedCount,
                'errors' => $errorCount,
            ], 200);
        } catch (\Exception $e) {
            \Log::error("Results import failed: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 422);
        }
    }


}
