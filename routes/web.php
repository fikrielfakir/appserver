<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('admin.login');
});

Route::prefix('admin')->middleware(['web.auth', 'role:admin,superadmin'])->group(function () {
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
});
