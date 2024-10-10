<?php

use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// User Routes
Route::post('/users', [UsersController::class, 'store']);
Route::get('/users', [UsersController::class, 'index']);
Route::get('/users/{id}', [UsersController::class, 'show']);
Route::put('/users/{id}', [UsersController::class, 'update']);
Route::delete('users/{id}', [UsersController::class, 'destroy']);

// Authentication Route
Route::post('/authenticate', [UsersController::class, 'authenticateUser']);

// Transaction Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/transactions', [TransactionsController::class, 'store']);
    Route::get('/transactions', [TransactionsController::class, 'index']);
    Route::get('/transactions/{id}', [TransactionsController::class, 'show']);
    Route::put('/transactions/{id}', [TransactionsController::class, 'update']);
    Route::delete('/transactions/{id}', [TransactionsController::class, 'destroy']);
});
