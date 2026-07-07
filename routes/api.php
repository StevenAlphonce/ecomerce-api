<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Public routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Protected routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);
    Route::apiResource('categories', CategoryController::class)->except('index');
});


Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::apiResource('categories', CategoryController::class)->only('index');

Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');
