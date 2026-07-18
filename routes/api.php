<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::get('/countries', [ApiController::class, 'countries']);
Route::get('/country/{iso}', [ApiController::class, 'country']);
Route::get('/risk', [ApiController::class, 'risk']);
Route::get('/ports', [ApiController::class, 'ports']);
Route::get('/news', [ApiController::class, 'news']);
Route::get('/currency', [ApiController::class, 'currency']);
Route::get('/marine', [ApiController::class, 'marine']);
