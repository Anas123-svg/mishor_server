<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::post('/admin/register', [AdminController::class, 'signUp']);
Route::post('/admin/login', [AdminController::class, 'login']);
Route::get('/admin/{id}', [AdminController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
    Route::get('by-token/admin/', [AdminController::class, 'getAuthenticatedAdmin']);
    Route::put('/admin/{id}', [AdminController::class, 'update']);
    Route::put('update/by-token/admin', [AdminController::class, 'updateAuthenticated']);
    Route::delete('/admin/{id}', [AdminController::class, 'destroy']);
});





































