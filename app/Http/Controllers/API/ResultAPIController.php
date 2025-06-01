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
        $results = $this->resultRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );
        $results = Result::with(['sclass', 'exam','student','subject'])->get();

        return $this->sendResponse($results->toArray(), 'Results retrieved successfully');
    }

    /**
     * Store a newly created Result in storage.
     * POST /results
     */

    public function store(CreateResultAPIRequest $request): JsonResponse
{
    $input = $request->only(['class_id', 'subject_id', 'exam_id']);
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

        Excel::import(new ResultsImport, $request->file('file'));

        return response()->json(['message' => 'Results imported successfully'], 200);
    }


}
