<?php

use App\Http\Controllers\Api\AnimalController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DiseaseReportController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\UserAnimalController;
use App\Http\Controllers\Api\VaccineScheduleController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

Route::get('health', fn () => response()->json([
    'status' => __('api.health_ok'),
    'app' => config('app.name'),
    'version' => '1.0.0',
    'time' => now()->toIso8601String(),
]));

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('send-otp', [AuthController::class, 'sendOtp']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('fcm-token', [AuthController::class, 'updateFcmToken']);
        Route::get('me', [AuthController::class, 'me']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
    });
});

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return response()->json(['message' => __('api.email_verified')]);
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

Route::prefix('locations')->group(function () {
    Route::get('countries', [LocationController::class, 'countries']);
    Route::get('countries/{country}/governorates', [LocationController::class, 'governorates']);
    Route::get('governorates/{governorate}/cities', [LocationController::class, 'cities']);
    Route::get('cities/{city}/regions', [LocationController::class, 'regions']);
});

Route::prefix('animals')->group(function () {
    Route::get('categories', [AnimalController::class, 'categories']);
    Route::get('categories/{category}', [AnimalController::class, 'byCategory']);
    Route::get('{animal}', [AnimalController::class, 'show']);
});

Route::get('reports/approved', [DiseaseReportController::class, 'approved']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('my-animals')->group(function () {
        Route::get('/', [UserAnimalController::class, 'index']);
        Route::post('/', [UserAnimalController::class, 'store']);
        Route::get('{userAnimal}', [UserAnimalController::class, 'show']);
        Route::delete('{userAnimal}', [UserAnimalController::class, 'destroy']);
    });

    Route::prefix('vaccine-schedules')->group(function () {
        Route::get('/', [VaccineScheduleController::class, 'index']);
        Route::patch('{schedule}/mark-done', [VaccineScheduleController::class, 'markDone']);
    });

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('unread-count', [NotificationController::class, 'unreadCount']);
        Route::patch('{id}/read', [NotificationController::class, 'markRead']);
        Route::post('mark-all-read', [NotificationController::class, 'markAllRead']);
    });

    Route::prefix('reports')->group(function () {
        Route::get('/', [DiseaseReportController::class, 'index']);
        Route::post('/', [DiseaseReportController::class, 'store']);
        Route::get('{report}', [DiseaseReportController::class, 'show']);
    });
});
