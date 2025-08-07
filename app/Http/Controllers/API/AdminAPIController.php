<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\School;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

/**
 * Class AdminAPIController
 */
class AdminAPIController extends AppBaseController
{
    /**
     * Display a listing of all users with their schools.
     * GET|HEAD /admin/users
     */
    public function getUsers(Request $request): JsonResponse
    {
        // Additional check to ensure only super_admin can access
        if (!auth()->user()->isSuperAdmin()) {
            return $this->sendError('Access denied. Only super administrators can access this resource.', [], 403);
        }

        // Bypass the SchoolScope to get all users
        $users = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)->with('school')->get();
        return $this->sendResponse($users->toArray(), 'Users retrieved successfully');
    }

    /**
     * Display a listing of all schools with their users.
     * GET|HEAD /admin/schools
     */
    public function getSchools(Request $request): JsonResponse
    {
        // Bypass the SchoolScope to get all schools
        $schools = School::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)->with('users')->get();
        return $this->sendResponse($schools->toArray(), 'Schools retrieved successfully');
    }

    /**
     * Assign a school to a user.
     * POST /admin/assign-school
     */
    public function assignSchool(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'school_id' => 'required|exists:schools,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $user = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)->find($request->user_id);
        $school = School::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)->find($request->school_id);

        $user->school_id = $school->id;
        $user->save();

        return $this->sendResponse($user->load('school')->toArray(), 'School assigned successfully');
    }

    /**
     * Create a new user with school assignment.
     * POST /admin/create-user
     */
    public function createUser(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username',
            'phone_number' => 'required|string|unique:users,phone_number',
            'password' => 'required|string|min:6',
            'userType' => 'nullable|in:admin,client',
            'school_id' => 'required|exists:schools,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'userType' => $request->get('userType', 'client'),
            'school_id' => $request->school_id,
        ]);

        return $this->sendResponse($user->load('school')->toArray(), 'User created successfully');
    }

    /**
     * Update user information.
     * PUT/PATCH /admin/update-user/{id}
     */
    public function updateUser($id, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'username' => 'sometimes|required|string|unique:users,username,' . $id,
            'phone_number' => 'sometimes|required|string|unique:users,phone_number,' . $id,
            'userType' => 'sometimes|required|in:admin,client',
            'school_id' => 'sometimes|required|exists:schools,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $user = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)->find($id);

        if (empty($user)) {
            return $this->sendError('User not found');
        }

        $user->update($request->all());

        return $this->sendResponse($user->load('school')->toArray(), 'User updated successfully');
    }

    /**
     * Remove user's school assignment.
     * DELETE /admin/remove-school/{user_id}
     */
    public function removeSchoolAssignment($user_id): JsonResponse
    {
        $user = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)->find($user_id);

        if (empty($user)) {
            return $this->sendError('User not found');
        }

        $user->school_id = null;
        $user->save();

        return $this->sendResponse($user->load('school')->toArray(), 'School assignment removed successfully');
    }

    /**
     * Get statistics for admin dashboard.
     * GET /admin/statistics
     */
    public function getStatistics(): JsonResponse
    {
        // Bypass the SchoolScope to get accurate statistics
        $stats = [
            'total_schools' => School::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)->count(),
            'total_users' => User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)->count(),
            'total_admins' => User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)->where('userType', 'admin')->count(),
            'total_clients' => User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)->where('userType', 'client')->count(),
            'schools_with_users' => School::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)->whereHas('users')->count(),
            'schools_without_users' => School::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)->whereDoesntHave('users')->count(),
        ];

        return $this->sendResponse($stats, 'Statistics retrieved successfully');
    }
}