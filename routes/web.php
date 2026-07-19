<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AdminController;
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
        $userId = auth()->id();
        $favorites = \Illuminate\Support\Facades\DB::table('countries')
            ->join('watchlists', 'countries.id', '=', 'watchlists.country_id')
            ->where('watchlists.user_id', $userId)
            ->select('countries.*')
            ->get();
        return view('favorites', compact('favorites'));
    });

    Route::get('/admin', function () {
        return view('admin');
    });

    // API Routes (moved here to access Session/Auth)
    Route::get('/api/countries', [ApiController::class, 'countries']);
    Route::get('/api/country/{iso}', [ApiController::class, 'country']);
    Route::get('/api/risk', [ApiController::class, 'risk']);
    Route::get('/api/ports', [ApiController::class, 'ports']);
    Route::get('/api/news', [ApiController::class, 'news']);
    Route::get('/api/currency', [ApiController::class, 'currency']);
    Route::get('/api/marine', [ApiController::class, 'marine']);
    Route::post('/api/country/{iso}/favorite', [ApiController::class, 'toggleFavorite']);

    // Admin CRUD Routes
    Route::get('/api/admin/users', [AdminController::class, 'getUsers']);
    Route::delete('/api/admin/users/{id}', [AdminController::class, 'deleteUser']);
    
    Route::get('/api/admin/ports', [AdminController::class, 'getPorts']);
    Route::post('/api/admin/ports', [AdminController::class, 'savePort']);
    Route::delete('/api/admin/ports/{id}', [AdminController::class, 'deletePort']);
    
    Route::get('/api/admin/articles', [AdminController::class, 'getArticles']);
    Route::post('/api/admin/articles', [AdminController::class, 'saveArticle']);
    Route::delete('/api/admin/articles/{id}', [AdminController::class, 'deleteArticle']);
});

Route::get('/seed-data', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate:fresh');
    \Illuminate\Support\Facades\Artisan::call('db:seed');
    return "Database has been freshly migrated and seeded with dummy data! <a href='/'>Go to Dashboard</a>";
});

