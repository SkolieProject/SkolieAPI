<?php

use App\Http\Controllers\AssayController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register'])->middleware(['auth:sanctum', AdminMiddleware::class]);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
});



Route::prefix('assay')->group(function () {  
    Route::get('/get{query}', [AssayController::class, 'getAssays']);
    Route::post('/new', [AssayController::class, 'newAssay']);
    Route::patch('/write', [AssayController::class, 'rewriteAssay']);
    Route::delete('/erase', [AssayController::class, 'eraseAssay']);
})->middleware('auth:sanctum');
