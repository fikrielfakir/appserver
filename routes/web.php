<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/admin/dashboard');
    }
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    });
    
    Route::get('/apps', function () {
        return view('admin.apps.index');
    });
    
    Route::get('/notifications', function () {
        return view('admin.notifications.index');
    });
    
    Route::get('/admob-accounts', function () {
        return view('admin.admob-accounts.index');
    });
    
    Route::get('/analytics', function () {
        return view('admin.analytics');
    });
    
    Route::get('/settings', function () {
        return view('admin.settings.index');
    });
});
