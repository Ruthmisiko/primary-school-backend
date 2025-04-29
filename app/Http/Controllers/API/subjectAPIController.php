<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatesubjectAPIRequest;
use App\Http\Requests\API\UpdatesubjectAPIRequest;
use App\Models\subject;
use App\Repositories\subjectRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class subjectAPIController
 */
class subjectAPIController extends AppBaseController
{
    private subjectRepository $subjectRepository;

    public function __construct(subjectRepository $subjectRepo)
    {
        $this->subjectRepository = $subjectRepo;
    }

    /**
     * Display a listing of the subjects.
     * GET|HEAD /subjects
     */
    public function index(Request $request): JsonResponse
    {
        $subjects = $this->subjectRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($subjects->toArray(), 'Subjects retrieved successfully');
    }

    /**
     * Store a newly created subject in storage.
     * POST /subjects
     */
    public function store(CreatesubjectAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $subject = $this->subjectRepository->create($input);

        return $this->sendResponse($subject->toArray(), 'Subject saved successfully');
    }

    /**
     * Display the specified subject.
     * GET|HEAD /subjects/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var subject $subject */
        $subject = $this->subjectRepository->find($id);

        if (empty($subject)) {
            return $this->sendError('Subject not found');
        }

        return $this->sendResponse($subject->toArray(), 'Subject retrieved successfully');
    }

    /**
     * Update the specified subject in storage.
     * PUT/PATCH /subjects/{id}
     */
    public function update($id, UpdatesubjectAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var subject $subject */
        $subject = $this->subjectRepository->find($id);

        if (empty($subject)) {
            return $this->sendError('Subject not found');
        }

        $subject = $this->subjectRepository->update($input, $id);

        return $this->sendResponse($subject->toArray(), 'subject updated successfully');
    }

    /**
     * Remove the specified subject from storage.
     * DELETE /subjects/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var subject $subject */
        $subject = $this->subjectRepository->find($id);

        if (empty($subject)) {
            return $this->sendError('Subject not found');
        }

        $subject->delete();

        return $this->sendSuccess('Subject deleted successfully');
    }
}
