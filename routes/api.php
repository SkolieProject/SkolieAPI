<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\AssayController;
use App\Http\Controllers\AssaySettingsController;
use App\Http\Controllers\AuthController;

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AssayAcessMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::prefix('admin/user')->group(function () {
    Route::get('/', [AdminController::class, 'getUsers'])->middleware(['auth:sanctum', AdminMiddleware::class]);
    Route::get('/{id}', [AdminController::class, 'getUser'])->middleware(['auth:sanctum', AdminMiddleware::class]);
    Route::post('/', [AdminController::class, 'register'])->middleware(['auth:sanctum', AdminMiddleware::class]);
    Route::patch('/{id}', [AdminController::class, 'rewriteUser'])->middleware(['auth:sanctum', AdminMiddleware::class]);
    Route::delete('/{id}', [AdminController::class, 'eraseUser'])->middleware(['auth:sanctum', AdminMiddleware::class]);
});

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->post('/toggle/answer/{assay}', [AssaySettingsController::class, 'toggleAnswerability']);
Route::middleware('auth:sanctum')->post('/toggle/visible/{assay}', [AssaySettingsController::class, 'toggleVisibility']);


Route::middleware('auth:sanctum')->resource('assays', AssayController::class);
Route::middleware('auth:sanctum')->resource('answers', AnswerController::class)->except('delete');