<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\AccessTokenController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::resource('students', App\Http\Controllers\API\StudentAPIController::class)
    ->except(['create', 'edit']);

Route::resource('teachers', App\Http\Controllers\API\TeacherAPIController::class)
    ->except(['create', 'edit']);

Route::resource('sclasses', App\Http\Controllers\API\SclassAPIController::class)
    ->except(['create', 'edit']);

Route::resource('parents', App\Http\Controllers\API\ParentAPIController::class)
    ->except(['create', 'edit']);

Route::resource('cocurriculars', App\Http\Controllers\API\CocurricularAPIController::class)
    ->except(['create', 'edit']);

Route::resource('exams', App\Http\Controllers\API\ExamAPIController::class)
    ->except(['create', 'edit']);

Route::resource('exams', App\Http\Controllers\API\ExamAPIController::class)
    ->except(['create', 'edit']);

Route::resource('results', App\Http\Controllers\API\ResultAPIController::class);

Route::get('students/{id}/print-result', [App\Http\Controllers\API\StudentAPIController::class, 'printResult']);

Route::resource('subjects', App\Http\Controllers\API\subjectAPIController::class)
    ->except(['create', 'edit']);

Route::resource('dashboards', App\Http\Controllers\API\dashboardAPIController::class)
    ->except(['create', 'edit']);

Route::resource('payments', App\Http\Controllers\API\PaymentAPIController::class)
    ->except(['create', 'edit']);

//students import and export
Route::post('/import-students', [App\Http\Controllers\API\StudentAPIController::class, 'importStudents']);

Route::get('/download-students-template', [App\Http\Controllers\API\StudentAPIController::class, 'downloadTemplate']);

// results import and export

Route::post('/import-results', [App\Http\Controllers\API\ResultAPIController::class, 'importResults']);

Route::get('/download-results-template', [App\Http\Controllers\API\ResultAPIController::class, 'downloadTemplate']);


Route::resource('settings', App\Http\Controllers\API\SettingAPIController::class)
    ->except(['create', 'edit']);

Route::resource('users', App\Http\Controllers\API\UserAPIController::class);

//reports

Route::get('teachers/report/pdf', [App\Http\Controllers\API\TeacherAPIController::class, 'TeachersReportPdf']);

Route::get('students/report/pdf', [App\Http\Controllers\API\StudentAPIController::class, 'StudentsReportPdf']);
