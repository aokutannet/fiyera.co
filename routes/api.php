<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProposalController;
use App\Http\Middleware\ApiSetTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Tenant context required routes
    Route::middleware(ApiSetTenant::class)->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        
        Route::get('/proposals', [ProposalController::class, 'index']);
        Route::post('/proposals', [ProposalController::class, 'store']);
        Route::get('/proposals/statuses', [ProposalController::class, 'statuses']);
        Route::get('/proposals/{id}/history', [ProposalController::class, 'history']);
        Route::post('/proposals/{id}/status', [ProposalController::class, 'updateStatus']);
        Route::get('/proposals/{id}', [ProposalController::class, 'show']);
        Route::put('/proposals/{id}', [ProposalController::class, 'update']);
        Route::delete('/proposals/{id}', [ProposalController::class, 'destroy']);
        Route::get('/dashboard/stats', [\App\Http\Controllers\Api\DashboardController::class, 'stats']);
        Route::get('/customers', [\App\Http\Controllers\Api\CustomerController::class, 'index']);
        Route::post('/customers', [\App\Http\Controllers\Api\CustomerController::class, 'store']);
        Route::get('/customers/{id}', [\App\Http\Controllers\Api\CustomerController::class, 'show']);
        Route::put('/customers/{id}', [\App\Http\Controllers\Api\CustomerController::class, 'update']);
        Route::delete('/customers/{id}', [\App\Http\Controllers\Api\CustomerController::class, 'destroy']);
        Route::get('/products', [\App\Http\Controllers\Api\ProductController::class, 'index']);
        Route::post('/products', [\App\Http\Controllers\Api\ProductController::class, 'store']);
        Route::get('/products/{id}', [\App\Http\Controllers\Api\ProductController::class, 'show']);
        Route::put('/products/{id}', [\App\Http\Controllers\Api\ProductController::class, 'update']);
        Route::delete('/products/{id}', [\App\Http\Controllers\Api\ProductController::class, 'destroy']);
        Route::get('/categories', [\App\Http\Controllers\Api\CategoryController::class, 'index']);
        Route::post('/categories', [\App\Http\Controllers\Api\CategoryController::class, 'store']);
        Route::get('/reports', [\App\Http\Controllers\Api\ReportController::class, 'index']);
        Route::get('/profile', [\App\Http\Controllers\Api\ProfileController::class, 'show']);
        Route::put('/profile', [\App\Http\Controllers\Api\ProfileController::class, 'update']);

        // User Management
        Route::get('/users', [\App\Http\Controllers\Api\UserController::class, 'index']);
        Route::post('/users', [\App\Http\Controllers\Api\UserController::class, 'store']);
        Route::put('/users/{id}', [\App\Http\Controllers\Api\UserController::class, 'update']);
        Route::delete('/users/{id}', [\App\Http\Controllers\Api\UserController::class, 'destroy']);
        Route::patch('/users/{id}/status', [\App\Http\Controllers\Api\UserController::class, 'toggleStatus']);

        // Settings
        Route::get('/settings', [\App\Http\Controllers\Api\SettingController::class, 'index']);
        Route::post('/settings', [\App\Http\Controllers\Api\SettingController::class, 'update']);

        // Subscription
        Route::get('/subscription', [\App\Http\Controllers\Api\SubscriptionController::class, 'index']);

        // Onboarding
        Route::get('/onboarding/status', [\App\Http\Controllers\Api\OnboardingController::class, 'checkStatus']);
        Route::get('/onboarding/questions', [App\Http\Controllers\Api\OnboardingController::class, 'questions']);
        Route::post('/onboarding/wizard', [App\Http\Controllers\Api\OnboardingController::class, 'storeWizard']);
        Route::post('/onboarding/company-details', [App\Http\Controllers\Api\OnboardingController::class, 'storeCompanyDetails']);
        Route::get('/onboarding/plans', [App\Http\Controllers\Api\OnboardingController::class, 'plans']);
        Route::post('/onboarding/trial', [\App\Http\Controllers\Api\OnboardingController::class, 'startTrial']);
    });
});
