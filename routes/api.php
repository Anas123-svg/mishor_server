<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FolderController;

Route::post('/admin/register', [AdminController::class, 'signUp']);
Route::post('/admin/login', [AdminController::class, 'login']);
Route::get('/admin/{id}', [AdminController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
    Route::get('by-token/admin/', [AdminController::class, 'getAuthenticatedAdmin']);
    Route::put('/admin/{id}', [AdminController::class, 'update']);
    Route::put('update/by-token/admin', [AdminController::class, 'updateAuthenticated']);
    Route::delete('/admin/{id}', [AdminController::class, 'destroy']);
    Route::post('/admin/logout', [AdminController::class, 'logout']);
});



Route::post('/client/register', [ClientController::class, 'signUp']);
Route::post('/client/login', [ClientController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/client', [ClientController::class, 'index']);
    Route::get('by-token/client', [ClientController::class, 'getAuthenticatedClient']);
    Route::get('/client/{id}', [ClientController::class, 'show']);
    Route::put('/client/{id}', [ClientController::class, 'update']);
    Route::put('update/by-token/client', [ClientController::class, 'updateAuthenticated']);
    Route::delete('/client/{id}', [ClientController::class, 'destroy']);
    Route::post('/client/logout', [ClientController::class, 'logout']);
});



Route::middleware('auth:sanctum')->group(function () {
    Route::post('add/folder', [FolderController::class, 'createFolder']);
    Route::post('/folders/upload', [FolderController::class, 'uploadFile']);
    Route::get('/folders/{id}/contents', [FolderController::class, 'getFolderContents']);
    Route::get('folders/client', [FolderController::class, 'getAllFoldersWithContents']);
});























