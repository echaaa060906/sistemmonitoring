<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    });

    Route::get('/map', function () {
        return view('map');
    });

    Route::get('/comparison', function () {
        return view('comparison');
    });

    Route::get('/ports', function () {
        return view('ports');
    });

    Route::get('/favorites', function () {
        return view('favorites');
    });

    Route::get('/admin', function () {
        return view('admin');
    });
});

Route::get('/seed-data', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate:fresh');
    \Illuminate\Support\Facades\Artisan::call('db:seed');
    return "Database has been freshly migrated and seeded with dummy data! <a href='/'>Go to Dashboard</a>";
});

