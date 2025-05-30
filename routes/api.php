<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\AccessTokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/auth/register', [AuthController::class, 'register']);

Route::post('/auth/login', [AccessTokenController::class, 'issueToken']);

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

Route::resource('results', App\Http\Controllers\API\ResultAPIController::class);

Route::resource('subjects', App\Http\Controllers\API\subjectAPIController::class)
    ->except(['create', 'edit']);

Route::resource('dashboards', App\Http\Controllers\API\dashboardAPIController::class)
    ->except(['create', 'edit']);

Route::resource('payments', App\Http\Controllers\API\PaymentAPIController::class)
    ->except(['create', 'edit']);

Route::post('/import-students', [App\Http\Controllers\API\StudentAPIController::class, 'importStudents']);

Route::get('/download-students-template', [App\Http\Controllers\API\StudentAPIController::class, 'downloadTemplate']);
