<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssayController;
use App\Http\Controllers\AuthController;

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AssayAcessMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::prefix('admin/user')->group(function() {
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

Route::middleware('auth:sanctum')->prefix('assay')->group(function () {  
    Route::get('/', [AssayController::class, 'getAssays'])->middleware('can:view,assay');
    Route::get('/{assay}', [AssayController::class, 'getAssay'])->middleware('can:view,assay');
    Route::post('/', [AssayController::class, 'newAssay'])->middleware(['can:create,assay', AssayAcessMiddleware::class]);
    Route::patch('/', [AssayController::class, 'rewriteAssay'])->middleware([AssayAcessMiddleware::class, 'can:update,assay']);
    Route::delete('/{assay}', [AssayController::class, 'eraseAssay'])->middleware([AssayAcessMiddleware::class, 'can:delete,assay']);
    Route::post('/view/{assay}', [AssayController::class, 'toggleViewAssay'])->middleware([AssayAcessMiddleware::class, 'can:update,assay']);
    Route::post('/answer/{assay}', [AssayController::class, 'toggleAnswerAssay'])->middleware([AssayAcessMiddleware::class, 'can:update,assay']);   
});
