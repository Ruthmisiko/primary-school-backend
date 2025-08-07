<?php

namespace App\Http\Controllers\API;

use App\Models\School;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Validator;

/**
 * Class SchoolAPIController
 */
class SchoolAPIController extends AppBaseController
{
    /**
     * Display a listing of the Schools.
     * GET|HEAD /schools
     */
    public function index(Request $request): JsonResponse
    {
        // Bypass the SchoolScope to get all schools for admin
        $schools = School::all();
        return $this->sendResponse($schools->toArray(), 'Schools retrieved successfully');
    }

    /**
     * Store a newly created School in storage.
     * POST /schools
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $school = School::create($request->all());

        return $this->sendResponse($school->toArray(), 'School saved successfully');
    }

    /**
     * Display the specified School.
     * GET|HEAD /schools/{id}
     */
    public function show($id): JsonResponse
    {
        $school = School::find($id);

        if (empty($school)) {
            return $this->sendError('School not found');
        }

        return $this->sendResponse($school->toArray(), 'School retrieved successfully');
    }

    /**
     * Update the specified School in storage.
     * PUT/PATCH /schools/{id}
     */
    public function update($id, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $school = School::find($id);

        if (empty($school)) {
            return $this->sendError('School not found');
        }

        $school->update($request->all());

        return $this->sendResponse($school->toArray(), 'School updated successfully');
    }

    /**
     * Remove the specified School from storage.
     * DELETE /schools/{id}
     */
    public function destroy($id): JsonResponse
    {
        $school = School::find($id);

        if (empty($school)) {
            return $this->sendError('School not found');
        }

        // Check if school has associated users
        if ($school->users()->count() > 0) {
            return $this->sendError('Cannot delete school. It has associated users.');
        }

        // Check if school has associated students
        if ($school->students()->count() > 0) {
            return $this->sendError('Cannot delete school. It has associated students.');
        }

        // Check if school has associated teachers
        if ($school->teachers()->count() > 0) {
            return $this->sendError('Cannot delete school. It has associated teachers.');
        }

        $school->delete();

        return $this->sendSuccess('School deleted successfully');
    }
} 