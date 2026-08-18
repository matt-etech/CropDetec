<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\FarmerPortalController;
use App\Http\Controllers\WebAuthController;
use App\Models\ApiToken;
use App\Models\Crop;
use App\Models\Diagnosis;
use App\Models\Disease;
use App\Models\Treatment;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    $count = fn (string $table, string $model) => Schema::hasTable($table)
        ? $model::count()
        : 0;

    return view('welcome', [
        'stats' => [
            'users' => $count('users', User::class),
            'apiTokens' => $count('api_tokens', ApiToken::class),
            'crops' => $count('crops', Crop::class),
            'diseases' => $count('diseases', Disease::class),
            'treatments' => $count('treatments', Treatment::class),
            'diagnoses' => $count('diagnoses', Diagnosis::class),
        ],
        'apiRoutes' => [
            'POST /api/register',
            'POST /api/login',
            'POST /api/logout',
            'GET /api/me',
            'PATCH /api/me',
            'GET /api/crops',
            'GET /api/diseases',
            'POST /api/diagnoses',
            'GET /api/diagnoses',
            'GET /api/diagnoses/{diagnosis}',
            'GET /api/admin/crops',
            'POST /api/admin/crops',
            'PATCH /api/admin/crops/{crop}',
            'GET /api/admin/diseases',
            'POST /api/admin/diseases',
            'PATCH /api/admin/diseases/{disease}',
            'GET /api/admin/treatments',
            'POST /api/admin/treatments',
            'PATCH /api/admin/treatments/{treatment}',
            'GET /api/admin/users',
            'GET /api/admin/diagnoses',
        ],
    ]);
});

Route::get('/roadmap', function () {
    return response()->file(base_path('ROADMAP.md'), [
        'Content-Type' => 'text/markdown; charset=UTF-8',
    ]);
});

Route::get('/register', [WebAuthController::class, 'showRegister']);
Route::post('/register', [WebAuthController::class, 'register']);
Route::get('/login', [WebAuthController::class, 'showLogin']);
Route::post('/login', [WebAuthController::class, 'login']);
Route::get('/dashboard', [WebAuthController::class, 'dashboard']);
Route::post('/logout', [WebAuthController::class, 'logout']);

Route::get('/diagnose', [FarmerPortalController::class, 'createDiagnosis']);
Route::post('/diagnose', [FarmerPortalController::class, 'storeDiagnosis']);
Route::get('/diagnoses', [FarmerPortalController::class, 'history']);
Route::get('/diagnoses/{diagnosis}', [FarmerPortalController::class, 'showDiagnosis']);
Route::get('/crops', [FarmerPortalController::class, 'cropLibrary']);
Route::get('/profile', [FarmerPortalController::class, 'profile']);
Route::post('/profile', [FarmerPortalController::class, 'updateProfile']);

Route::get('/admin', [AdminDashboardController::class, 'index']);
Route::post('/admin/crops', [AdminDashboardController::class, 'storeCrop']);
Route::post('/admin/diseases', [AdminDashboardController::class, 'storeDisease']);
Route::post('/admin/treatments', [AdminDashboardController::class, 'storeTreatment']);
Route::post('/admin/users/{user}/role', [AdminDashboardController::class, 'updateUserRole']);
