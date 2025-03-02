<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTeacherAPIRequest;
use App\Http\Requests\API\UpdateTeacherAPIRequest;
use App\Models\Teacher;
use App\Repositories\TeacherRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class TeacherAPIController
 */
class TeacherAPIController extends AppBaseController
{
    private TeacherRepository $teacherRepository;

    public function __construct(TeacherRepository $teacherRepo)
    {
        $this->teacherRepository = $teacherRepo;
    }

    /**
     * Display a listing of the Teachers.
     * GET|HEAD /teachers
     */
    public function index(Request $request): JsonResponse
    {
        $teachers = $this->teacherRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($teachers->toArray(), 'Teachers retrieved successfully');
    }

    /**
     * Store a newly created Teacher in storage.
     * POST /teachers
     */
    public function store(CreateTeacherAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $teacher = $this->teacherRepository->create($input);

        return $this->sendResponse($teacher->toArray(), 'Teacher saved successfully');
    }

    /**
     * Display the specified Teacher.
     * GET|HEAD /teachers/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Teacher $teacher */
        $teacher = $this->teacherRepository->find($id);

        if (empty($teacher)) {
            return $this->sendError('Teacher not found');
        }

        return $this->sendResponse($teacher->toArray(), 'Teacher retrieved successfully');
    }

    /**
     * Update the specified Teacher in storage.
     * PUT/PATCH /teachers/{id}
     */
    public function update($id, UpdateTeacherAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Teacher $teacher */
        $teacher = $this->teacherRepository->find($id);

        if (empty($teacher)) {
            return $this->sendError('Teacher not found');
        }

        $teacher = $this->teacherRepository->update($input, $id);

        return $this->sendResponse($teacher->toArray(), 'Teacher updated successfully');
    }

    /**
     * Remove the specified Teacher from storage.
     * DELETE /teachers/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Teacher $teacher */
        $teacher = $this->teacherRepository->find($id);

        if (empty($teacher)) {
            return $this->sendError('Teacher not found');
        }

        $teacher->delete();

        return $this->sendSuccess('Teacher deleted successfully');
    }
}
