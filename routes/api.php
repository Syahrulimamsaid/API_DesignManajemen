<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
use App\Http\Controllers\TimeLinesController;
use App\Http\Controllers\DataPendukungController;
use App\Http\Controllers\JobAssignmentController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\QualityControlController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthenticationController::class, 'logout']);
    Route::post('/job', [JobController::class, 'store']);
    Route::get('/job', [JobController::class, 'index']);
    Route::patch('/job/{kode}', [JobController::class, 'update']);
    Route::delete('/job/{kode}', [JobController::class, 'destroy']);
    Route::get('/getdatadash', [JobAssignmentController::class, 'getDataDash']);
    Route::get('/getjobfinalling', [JobAssignmentController::class, 'getDataJobFinalling']);
    Route::get('/jobcheck', [QualityControlController::class, 'job_checked']);
    Route::post('/jobrevision/{kode}', [QualityControlController::class, 'store']);
    Route::get('/jobrevision', [QualityControlController::class, 'index']);
    Route::patch('/joblolos/{id}', [JobController::class, 'jobLolos']);
    Route::get('/jobme', [JobAssignmentController::class, 'index']);
    Route::get('/joballme', [JobAssignmentController::class, 'getAllJob']);
    Route::patch('/takejob/{kode}', [JobAssignmentController::class, 'update']);
    Route::post('/jobresult/{kode}', [JobController::class, 'design_result']);
    Route::post('/data', [DataPendukungController::class, 'store']);
    Route::get('/timelines/{job_assignment_kode}', [TimeLinesController::class, 'show']);
    Route::patch('/jobrevisionschedulling/{kode}', [JobAssignmentController::class, 'jobRevisionScheduled']);
    Route::get('/data/{nama}', [DataPendukungController::class, 'show']);
    Route::get('/design/{nama}', [JobController::class, 'fileHasilDesign']);
    Route::get('/report', [AuthenticationController::class, 'report']);
    Route::get('/report/{kode}', [AuthenticationController::class, 'reportDesigner']);
    Route::get('/getjobpost', [JobController::class, 'getJobPost']);
    Route::delete('/data/{id}', [DataPendukungController::class, 'destroy']);
    Route::post ('/jobschedulling', [JobAssignmentController::class, 'jobSchedulling']);
    Route::get('/komen/{kode}', [QualityControlController::class, 'getKomentar']);
    Route::get('/designer', [AuthenticationController::class, 'designer']);
    Route::get('/allJob', [JobAssignmentController::class, 'getJob']);
    Route::patch('/finallingjob/{kode}',[JobController::class,'jobResponse']);
    Route::get('/jobrejected', [JobAssignmentController::class, 'jobRejected']);
    Route::get('/calendar', [TimeLinesController::class, 'calendar']);
});

Route::post('/login', [AuthenticationController::class, 'login']);
Route::post('/user', [AuthenticationController::class, 'store']);