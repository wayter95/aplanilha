<?php

use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DocumentTemplateController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\ProjectTypeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group and automatically prefixed with /api
|
*/

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('api.users.index');
    Route::post('/', [UserController::class, 'store'])->name('api.users.store');
    Route::get('/{id}', [UserController::class, 'show'])->name('api.users.show');
    Route::put('/{id}', [UserController::class, 'update'])->name('api.users.update');
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('api.users.destroy');
    Route::patch('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('api.users.toggle-status');
    Route::patch('/{id}/photo', [UserController::class, 'updatePhoto'])->name('api.users.update-photo');
    Route::get('/statistics', [UserController::class, 'statistics'])->name('api.users.statistics');
    Route::get('/roles', [UserController::class, 'roles'])->name('api.users.roles');
    Route::get('/export/csv', [UserController::class, 'exportCsv'])->name('api.users.export.csv');
});

/*
|--------------------------------------------------------------------------
| Role API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('roles')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('api.roles.index');
    Route::post('/', [RoleController::class, 'store'])->name('api.roles.store');
    Route::get('/{id}', [RoleController::class, 'show'])->name('api.roles.show');
    Route::put('/{id}', [RoleController::class, 'update'])->name('api.roles.update');
    Route::delete('/{id}', [RoleController::class, 'destroy'])->name('api.roles.destroy');
    Route::patch('/{id}/toggle-status', [RoleController::class, 'toggleStatus'])->name('api.roles.toggle-status');
    Route::get('/statistics', [RoleController::class, 'statistics'])->name('api.roles.statistics');
    Route::get('/permissions', [RoleController::class, 'permissions'])->name('api.roles.permissions');
    Route::get('/export/csv', [RoleController::class, 'exportCsv'])->name('api.roles.export.csv');
});

/*
|--------------------------------------------------------------------------
| File Upload Routes (S3)
|--------------------------------------------------------------------------
*/
Route::prefix('files')->group(function () {
    Route::post('/presigned-url', [FileUploadController::class, 'generatePresignedUrl'])->name('api.files.presigned-url');
    Route::post('/temporary-url', [FileUploadController::class, 'generateTemporaryUrl'])->name('api.files.temporary-url');
    Route::get('/signed-url', [FileUploadController::class, 'getSignedUrl'])->name('api.files.signed-url');
    Route::delete('/delete', [FileUploadController::class, 'deleteFile'])->name('api.files.delete');
});

/*
|--------------------------------------------------------------------------
| Document Types API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('document-types')->group(function () {
    Route::get('/', [DocumentTypeController::class, 'index'])->name('api.document-types.index');
    Route::get('/codes', [DocumentTypeController::class, 'codes'])->name('api.document-types.codes');
    Route::post('/', [DocumentTypeController::class, 'store'])->name('api.document-types.store');
    Route::get('/{id}', [DocumentTypeController::class, 'show'])->name('api.document-types.show');
    Route::put('/{id}', [DocumentTypeController::class, 'update'])->name('api.document-types.update');
    Route::delete('/{id}', [DocumentTypeController::class, 'destroy'])->name('api.document-types.destroy');
});

/*
|--------------------------------------------------------------------------
| Document Templates API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('document-templates')->group(function () {
    Route::get('/types', [DocumentTemplateController::class, 'types'])->name('api.document-templates.types');
    Route::get('/', [DocumentTemplateController::class, 'apiIndex'])->name('api.document-templates.index');
    Route::post('/', [DocumentTemplateController::class, 'store'])->name('api.document-templates.store');
    Route::get('/placeholders', [DocumentTemplateController::class, 'placeholders'])->name('api.document-templates.placeholders');
    Route::get('/{id}', [DocumentTemplateController::class, 'show'])->name('api.document-templates.show');
    Route::put('/{id}', [DocumentTemplateController::class, 'update'])->name('api.document-templates.update');
    Route::delete('/{id}', [DocumentTemplateController::class, 'destroy'])->name('api.document-templates.destroy');
    Route::post('/{id}/set-default', [DocumentTemplateController::class, 'setDefault'])->name('api.document-templates.set-default');
});

/*
|--------------------------------------------------------------------------
| Document Generation API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('document-generation')->group(function () {
    Route::post('/{id}/preview-html', [DocumentTemplateController::class, 'previewHtml'])->name('api.document-generation.preview-html');
    Route::get('/{id}/export-pdf', [DocumentTemplateController::class, 'exportPdf'])->name('api.document-generation.export-pdf');
});

/*
|--------------------------------------------------------------------------
| Project Types API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('project-types')->group(function () {
    Route::get('/', [ProjectTypeController::class, 'index'])->name('api.project-types.index');
    Route::post('/', [ProjectTypeController::class, 'store'])->name('api.project-types.store');
    Route::get('/{id}', [ProjectTypeController::class, 'show'])->name('api.project-types.show');
    Route::put('/{id}', [ProjectTypeController::class, 'update'])->name('api.project-types.update');
    Route::delete('/{id}', [ProjectTypeController::class, 'destroy'])->name('api.project-types.destroy');
    Route::patch('/{id}/activate', [ProjectTypeController::class, 'activate'])->name('api.project-types.activate');
    Route::patch('/{id}/block', [ProjectTypeController::class, 'block'])->name('api.project-types.block');
});

/*
|--------------------------------------------------------------------------
| User Settings API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('user')->group(function () {
    Route::put('/personal-data', [SettingsController::class, 'updatePersonalData'])->name('api.user.personal-data');
    Route::put('/password', [SettingsController::class, 'updatePassword'])->name('api.user.password');
});

/*
|--------------------------------------------------------------------------
| Company Settings API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('company')->group(function () {
    Route::put('/data', [SettingsController::class, 'updateCompanyData'])->name('api.company.data');
});
