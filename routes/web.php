<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('admin.login');
});

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    });
    
    Route::get('/apps', function () {
        return view('admin.apps.index');
    });
    
    Route::get('/notifications', function () {
        return view('admin.notifications.index');
    });
});
