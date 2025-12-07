<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ClientUserController;
//updated code yat
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
    Route::post('/admin/change-password', [AdminController::class, 'changePassword']);
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
    Route::post('/client/change-password', [AdminController::class, 'changePassword']);
});

Route::post('/admin/folder/create', [FolderController::class, 'createFolderByClientId']);
Route::post('/admin/file/upload', [FolderController::class, 'uploadFileByClientId']);
Route::get('/admin/folders/client/{id}', [FolderController::class, 'getAllFoldersWithContentsByClientId']);

Route::delete('/file/{id}', [FolderController::class, 'deleteFile']);
Route::get('/file/{id}', [FileController::class, 'get']);
Route::put('/file/{id}', [FileController::class, 'update']);
Route::delete('/folder/{id}', [FolderController::class, 'deleteFolder']);

Route::get('/folders/{id}/contents', [FolderController::class, 'getFolderContents']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('add/folder', [FolderController::class, 'createFolder']);
    Route::post('/folders/upload', [FolderController::class, 'uploadFile']);
    Route::get('folders/client', [FolderController::class, 'getAllFoldersWithContents']);
});


Route::get('/templates', [TemplateController::class, 'index']);           // Get all
Route::get('/templates/{id}', [TemplateController::class, 'show']);       // Get by ID
Route::post('/templates', [TemplateController::class, 'store']);          // Create
Route::put('/templates/{id}', [TemplateController::class, 'update']);     // Update
Route::delete('/templates/{id}', [TemplateController::class, 'destroy']); // Delete




Route::get('/clients/{id}/users', [ClientUserController::class, 'usersByClient']);
Route::get('/client-users', [ClientUserController::class, 'index']);
Route::post('/client-users', [ClientUserController::class, 'store']);
Route::get('/client-users/{id}', [ClientUserController::class, 'show']);
Route::put('/client-users/{id}', [ClientUserController::class, 'update']);
Route::delete('/client-users/{id}', [ClientUserController::class, 'destroy']);
