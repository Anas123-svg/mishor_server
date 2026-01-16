<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ClientUserController;
use App\Http\Controllers\UserAssignedFolderController;
use App\Http\Controllers\AppJobController;
use App\Http\Controllers\JobReportController;
//updated code yat 
Route::post('/admin/register', [AdminController::class, 'signUp']);
Route::post('/admin/login', [AdminController::class, 'login']);
Route::get('/admin/{id}', [AdminController::class, 'show']);
Route::get('/stat/admin', [AdminController::class, 'getAdminDashboardStats']);

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
Route::get('logs/client/{id}', [ClientController::class, 'getLoginsByClientId']);

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
Route::post('/client-user/login', [ClientUserController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/client-user/logout', [ClientUserController::class, 'logout']);
    Route::get('by-token/client-user', [ClientUserController::class, 'getAuthenticatedClient']);
    Route::put('by-token/client-user/update', [ClientUserController::class, 'updateAuthenticated']);
    Route::post('client-user/change-password', [ClientUserController::class, 'changePassword']);
    Route::get('get/client/users', [ClientUserController::class, 'usersByClientAuthenticated']);


    Route::get('/client-user/folders', [ClientUserController::class, 'getAllClientUserFoldersWithContents']);

});

Route::post('/client-users', [ClientUserController::class, 'store']);
Route::get('/client-users/{id}', [ClientUserController::class, 'show']);
Route::put('/client-users/{id}', [ClientUserController::class, 'update']);
Route::delete('/client-users/{id}', [ClientUserController::class, 'destroy']);


Route::get('client-user/{client_user_id}/folders', [UserAssignedFolderController::class, 'getByClientUser']);
Route::post('client-user/folders/assign', [UserAssignedFolderController::class, 'store']);
Route::put('client-user/{client_user_id}/folders', [UserAssignedFolderController::class, 'update']);
Route::delete('client-user/folder/{id}', [UserAssignedFolderController::class, 'destroy']);


Route::prefix('jobs')->group(function () {
    Route::get('/', [AppJobController::class, 'index']);            // List all jobs
    Route::get('/app-sync', [AppJobController::class, 'appSync']);          // List all job reports
    Route::post('/test-update', [AppJobController::class, 'updateTest']);          // List all job reports

    Route::get('/{id}', [AppJobController::class, 'show']);        // Get job by ID
    Route::get('/client/{clientId}', [AppJobController::class, 'getByClient']); // Get jobs by client ID
    Route::post('/', [AppJobController::class, 'store']);          // Create job
    Route::put('/{id}', [AppJobController::class, 'update']);      // Update job
    Route::delete('/{id}', [AppJobController::class, 'destroy']);  
});


Route::prefix('job-reports')->group(function () {
    Route::get('/', [JobReportController::class, 'index']);          // List all job reports
    Route::post('/', [JobReportController::class, 'store']);              // Create
    Route::get('/{id}', [JobReportController::class, 'show']);            // Get by report ID
    Route::get('/job/{jobId}', [JobReportController::class, 'getByJob']); // Get by job ID
    Route::put('/{id}', [JobReportController::class, 'update']);          // Update
    Route::delete('/{id}', [JobReportController::class, 'destroy']);      // Delete
});
