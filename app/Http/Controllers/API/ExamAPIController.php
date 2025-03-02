<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateExamAPIRequest;
use App\Http\Requests\API\UpdateExamAPIRequest;
use App\Models\Exam;
use App\Repositories\ExamRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class ExamAPIController
 */
class ExamAPIController extends AppBaseController
{
    private ExamRepository $examRepository;

    public function __construct(ExamRepository $examRepo)
    {
        $this->examRepository = $examRepo;
    }

    /**
     * Display a listing of the Exams.
     * GET|HEAD /exams
     */
    public function index(Request $request): JsonResponse
    {
        $exams = $this->examRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($exams->toArray(), 'Exams retrieved successfully');
    }

    /**
     * Store a newly created Exam in storage.
     * POST /exams
     */
    public function store(CreateExamAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $exam = $this->examRepository->create($input);

        return $this->sendResponse($exam->toArray(), 'Exam saved successfully');
    }

    /**
     * Display the specified Exam.
     * GET|HEAD /exams/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Exam $exam */
        $exam = $this->examRepository->find($id);

        if (empty($exam)) {
            return $this->sendError('Exam not found');
        }

        return $this->sendResponse($exam->toArray(), 'Exam retrieved successfully');
    }

    /**
     * Update the specified Exam in storage.
     * PUT/PATCH /exams/{id}
     */
    public function update($id, UpdateExamAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Exam $exam */
        $exam = $this->examRepository->find($id);

        if (empty($exam)) {
            return $this->sendError('Exam not found');
        }

        $exam = $this->examRepository->update($input, $id);

        return $this->sendResponse($exam->toArray(), 'Exam updated successfully');
    }

    /**
     * Remove the specified Exam from storage.
     * DELETE /exams/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Exam $exam */
        $exam = $this->examRepository->find($id);

        if (empty($exam)) {
            return $this->sendError('Exam not found');
        }

        $exam->delete();

        return $this->sendSuccess('Exam deleted successfully');
    }
}
