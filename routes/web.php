<?php
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectTypeController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\DocumentTemplateController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Login Routes
    Route::get('/sign-in', [AuthController::class, 'showLogin'])->name('signin');
    Route::get('/login', fn() => redirect()->route('signin'))->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // Password Reset Routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
});

Route::middleware(['auth'])->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users');
    
    // API Routes
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

Route::middleware(['auth'])->prefix('roles')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('roles');
    
    // API Routes
    Route::post('/', [RoleController::class, 'store'])->name('api.roles.store');
    Route::get('/{id}', [RoleController::class, 'show'])->name('api.roles.show');
    Route::put('/{id}', [RoleController::class, 'update'])->name('api.roles.update');
    Route::delete('/{id}', [RoleController::class, 'destroy'])->name('api.roles.destroy');
    Route::patch('/{id}/toggle-status', [RoleController::class, 'toggleStatus'])->name('api.roles.toggle-status');
    Route::get('/statistics', [RoleController::class, 'statistics'])->name('api.roles.statistics');
    Route::get('/permissions', [RoleController::class, 'permissions'])->name('api.roles.permissions');
    Route::get('/export/csv', [RoleController::class, 'exportCsv'])->name('api.roles.export.csv');
});

Route::middleware(['auth'])->prefix('contacts')->group(function () {
    Route::get('/', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('/create/{tempKey?}', [ContactController::class, 'create'])->name('contacts.create');
    Route::get('/{id}/edit', [ContactController::class, 'edit'])->name('contacts.edit');
    
    // API Routes
    Route::post('/', [\App\Http\Controllers\Api\ContactController::class, 'store'])->name('api.contacts.store');
    Route::get('/cities', [\App\Http\Controllers\Api\ContactController::class, 'cities'])->name('api.contacts.cities');
    Route::get('/countries', [\App\Http\Controllers\Api\ContactController::class, 'countries'])->name('api.contacts.countries');
    Route::get('/{id}', [\App\Http\Controllers\Api\ContactController::class, 'show'])->name('api.contacts.show');
    Route::put('/{id}', [\App\Http\Controllers\Api\ContactController::class, 'update'])->name('api.contacts.update');
    Route::delete('/{id}', [\App\Http\Controllers\Api\ContactController::class, 'destroy'])->name('api.contacts.destroy');
});

Route::middleware(['auth'])->prefix('projects')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->name('projects');
});

Route::middleware(['auth'])->prefix('project-types')->group(function () {
    Route::get('/', [ProjectTypeController::class, 'index'])->name('projects.types');
    Route::get('/new/{tempId}', [ProjectTypeController::class, 'create'])->name('projects.types.new');
    Route::get('/{id}/edit', [ProjectTypeController::class, 'edit'])->name('projects.types.edit');
    
    // API Routes
    Route::post('/', [ProjectTypeController::class, 'store'])->name('api.project-types.store');
    Route::get('/{id}', [ProjectTypeController::class, 'show'])->name('api.project-types.show');
    Route::put('/{id}', [ProjectTypeController::class, 'update'])->name('api.project-types.update');
    Route::delete('/{id}', [ProjectTypeController::class, 'destroy'])->name('api.project-types.destroy');
    Route::patch('/{id}/activate', [ProjectTypeController::class, 'activate'])->name('api.project-types.activate');
    Route::patch('/{id}/block', [ProjectTypeController::class, 'block'])->name('api.project-types.block');
});

Route::middleware(['auth'])->prefix('settings')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('settings');
    
    // API Routes - User Settings
    Route::put('/personal-data', [SettingsController::class, 'updatePersonalData'])->name('api.user.personal-data');
    Route::put('/password', [SettingsController::class, 'updatePassword'])->name('api.user.password');
    
    // API Routes - Company Settings
    Route::put('/company-data', [SettingsController::class, 'updateCompanyData'])->name('api.company.data');
});

Route::middleware(['auth'])->prefix('document-templates')->group(function () {
    Route::get('/', [DocumentTemplateController::class, 'index'])->name('document-templates');
    Route::get('/new/{tempId}', [DocumentTemplateController::class, 'create'])->name('document-templates.new');
    Route::get('/{id}/edit', [DocumentTemplateController::class, 'edit'])->name('document-templates.edit');
    
    // API Routes
    Route::get('/types', [DocumentTemplateController::class, 'types'])->name('api.document-templates.types');
    Route::post('/', [DocumentTemplateController::class, 'store'])->name('api.document-templates.store');
    Route::get('/placeholders', [DocumentTemplateController::class, 'placeholders'])->name('api.document-templates.placeholders');
    Route::get('/{id}', [DocumentTemplateController::class, 'show'])->name('api.document-templates.show');
    Route::put('/{id}', [DocumentTemplateController::class, 'update'])->name('api.document-templates.update');
    Route::delete('/{id}', [DocumentTemplateController::class, 'destroy'])->name('api.document-templates.destroy');
    Route::post('/{id}/set-default', [DocumentTemplateController::class, 'setDefault'])->name('api.document-templates.set-default');
    Route::post('/{id}/preview-html', [DocumentTemplateController::class, 'previewHtml'])->name('api.document-generation.preview-html');
    Route::get('/{id}/export-pdf', [DocumentTemplateController::class, 'exportPdf'])->name('api.document-generation.export-pdf');
});

Route::middleware(['auth'])->prefix('document-types')->group(function () {
    Route::get('/', [DocumentTypeController::class, 'index'])->name('document-types');
    Route::get('/new/{tempId}', [DocumentTypeController::class, 'create'])->name('document-types.new');
    Route::get('/{id}/edit', [DocumentTypeController::class, 'edit'])->name('document-types.edit');
    
    // API Routes
    Route::get('/codes', [DocumentTypeController::class, 'codes'])->name('api.document-types.codes');
    Route::post('/', [DocumentTypeController::class, 'store'])->name('api.document-types.store');
    Route::get('/{id}', [DocumentTypeController::class, 'show'])->name('api.document-types.show');
    Route::put('/{id}', [DocumentTypeController::class, 'update'])->name('api.document-types.update');
    Route::delete('/{id}', [DocumentTypeController::class, 'destroy'])->name('api.document-types.destroy');
});

Route::middleware(['auth'])->prefix('files')->group(function () {
    Route::post('/presigned-url', [FileUploadController::class, 'generatePresignedUrl'])->name('api.files.presigned-url');
    Route::post('/temporary-url', [FileUploadController::class, 'generateTemporaryUrl'])->name('api.files.temporary-url');
    Route::get('/signed-url', [FileUploadController::class, 'getSignedUrl'])->name('api.files.signed-url');
    Route::delete('/delete', [FileUploadController::class, 'deleteFile'])->name('api.files.delete');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});