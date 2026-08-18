<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\AdminCropController;
use App\Http\Controllers\Api\Admin\AdminDiseaseController;
use App\Http\Controllers\Api\Admin\AdminOverviewController;
use App\Http\Controllers\Api\Admin\AdminTreatmentController;
use App\Http\Controllers\Api\CropController;
use App\Http\Controllers\Api\DiagnosisController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => [
    'status' => 'ok',
    'service' => 'CropDetec API',
]);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/crops', [CropController::class, 'index']);
Route::get('/diseases', [CropController::class, 'diseases']);

Route::get('/me', [AuthController::class, 'me']);
Route::patch('/me', [AuthController::class, 'update']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::post('/diagnoses', [DiagnosisController::class, 'store'])->middleware('throttle:diagnoses');
Route::get('/diagnoses', [DiagnosisController::class, 'index'])->middleware('throttle:api');
Route::get('/diagnoses/{diagnosis}', [DiagnosisController::class, 'show'])->middleware('throttle:api');

Route::prefix('admin')->group(function () {
    Route::get('/users', [AdminOverviewController::class, 'users']);
    Route::get('/diagnoses', [AdminOverviewController::class, 'diagnoses']);

    Route::get('/crops', [AdminCropController::class, 'index']);
    Route::post('/crops', [AdminCropController::class, 'store']);
    Route::patch('/crops/{crop}', [AdminCropController::class, 'update']);

    Route::get('/diseases', [AdminDiseaseController::class, 'index']);
    Route::post('/diseases', [AdminDiseaseController::class, 'store']);
    Route::patch('/diseases/{disease}', [AdminDiseaseController::class, 'update']);

    Route::get('/treatments', [AdminTreatmentController::class, 'index']);
    Route::post('/treatments', [AdminTreatmentController::class, 'store']);
    Route::patch('/treatments/{treatment}', [AdminTreatmentController::class, 'update']);
});
