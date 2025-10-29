<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\AppController;
use App\Http\Controllers\Api\Admin\AdmobAccountController;
use App\Http\Controllers\Api\Admin\SwitchingRuleController;
use App\Http\Controllers\Api\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Api\V1\ConfigController;
use App\Http\Controllers\Api\V1\AnalyticsController;
use App\Http\Controllers\Api\V1\DeviceController;
use App\Http\Controllers\Api\V1\NotificationController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::prefix('admin')->middleware('auth:api')->group(function () {
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    
    Route::apiResource('apps', AppController::class);
    Route::apiResource('admob-accounts', AdmobAccountController::class);
    
    Route::get('/switching-rules/{appId}', [SwitchingRuleController::class, 'show']);
    Route::post('/switching-rules', [SwitchingRuleController::class, 'store']);
    Route::put('/switching-rules/{appId}', [SwitchingRuleController::class, 'update']);
    
    Route::apiResource('notifications', AdminNotificationController::class);
    Route::post('/notifications/{id}/send', [AdminNotificationController::class, 'send']);
});

Route::prefix('v1')->group(function () {
    Route::get('/config/{packageName}', [ConfigController::class, 'getConfig']);
    Route::post('/analytics/admob', [AnalyticsController::class, 'track']);
    Route::post('/device/register', [DeviceController::class, 'register']);
    Route::get('/notifications/pending', [NotificationController::class, 'pending']);
    Route::post('/notifications/track', [NotificationController::class, 'track']);
});
