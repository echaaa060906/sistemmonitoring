<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/map', function () {
    return view('map');
});

// Comparison Engine view (PDF Requirement 8)
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

Route::get('/seed-data', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate:fresh');
    \Illuminate\Support\Facades\Artisan::call('db:seed');
    return "Database has been freshly migrated and seeded with dummy data! <a href='/'>Go to Dashboard</a>";
});

