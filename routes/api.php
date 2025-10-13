<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\AccessTokenController;
use App\Http\Controllers\API\SettingAPIController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//authentication

Route::post('/auth/register', [AuthController::class, 'register']);

Route::post('/auth/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

Route::resource('students', App\Http\Controllers\API\StudentAPIController::class);

Route::resource('parents', App\Http\Controllers\API\ParentAPIController::class);

Route::resource('teachers', App\Http\Controllers\API\TeacherAPIController::class);

Route::resource('sclasses', App\Http\Controllers\API\SclassAPIController::class);

Route::resource('parents', App\Http\Controllers\API\ParentAPIController::class);

Route::resource('cocurriculars', App\Http\Controllers\API\CocurricularAPIController::class);

Route::resource('exams', App\Http\Controllers\API\ExamAPIController::class);

Route::resource('exams', App\Http\Controllers\API\ExamAPIController::class);

Route::resource('results', App\Http\Controllers\API\ResultAPIController::class);

Route::get('students/{id}/print-result', [App\Http\Controllers\API\StudentAPIController::class, 'printResult']);

Route::resource('subjects', App\Http\Controllers\API\subjectAPIController::class);

Route::resource('dashboards', App\Http\Controllers\API\dashboardAPIController::class);
Route::get('dashboard/enrollment-stats', [App\Http\Controllers\API\dashboardAPIController::class, 'getEnrollmentStats']);

Route::resource('payments', App\Http\Controllers\API\PaymentAPIController::class);
//students import and export
Route::post('/import-students', [App\Http\Controllers\API\StudentAPIController::class, 'importStudents']);

Route::get('/download-students-template', [App\Http\Controllers\API\StudentAPIController::class, 'downloadTemplate']);

Route::post('/import-results', [App\Http\Controllers\API\ResultAPIController::class, 'importResults']);

Route::get('/download-results-template', [App\Http\Controllers\API\ResultAPIController::class, 'downloadTemplate']);

Route::get('/settings', [SettingAPIController::class, 'index']);
Route::patch('/settings', [SettingAPIController::class, 'store']);
Route::post('/settings', [SettingAPIController::class, 'store']);
Route::get('/settings/logo', [SettingAPIController::class, 'getLogo']);

Route::resource('users', App\Http\Controllers\API\UserAPIController::class);

Route::resource('schools', App\Http\Controllers\API\SchoolAPIController::class)
    ->middleware('userType:super_admin,admin');

// Admin routes - Only super_admin can access
Route::prefix('admin')->middleware('userType:super_admin')->group(function () {
    Route::get('/users', [App\Http\Controllers\API\AdminAPIController::class, 'getUsers']);
    Route::get('/schools', [App\Http\Controllers\API\AdminAPIController::class, 'getSchools']);
    Route::post('/assign-school', [App\Http\Controllers\API\AdminAPIController::class, 'assignSchool']);
    Route::post('/create-user', [App\Http\Controllers\API\AdminAPIController::class, 'createUser']);
    Route::patch('/update-user/{id}', [App\Http\Controllers\API\AdminAPIController::class, 'updateUser']);
    Route::delete('/remove-school/{user_id}', [App\Http\Controllers\API\AdminAPIController::class, 'removeSchoolAssignment']);
    Route::get('/statistics', [App\Http\Controllers\API\AdminAPIController::class, 'getStatistics']);
});

//reports

Route::get('teachers/report/pdf', [App\Http\Controllers\API\TeacherAPIController::class, 'TeachersReportPdf']);

Route::get('students/report/pdf', [App\Http\Controllers\API\StudentAPIController::class, 'StudentsReportPdf']);

Route::post('/payments/initiate', [App\Http\Controllers\API\PaymentAPIController::class, 'initiatePayment']);

Route::post('/pesapal/callback', [App\Http\Controllers\API\PaymentAPIController::class, 'handleCallback']);

Route::resource('payment-methods', App\Http\Controllers\API\PaymentMethodAPIController::class);
});