<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StoreController;

Route::post('/stores/register', [StoreController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/returns/create', []);
});

Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
    Route::post('/companies/create', []);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
