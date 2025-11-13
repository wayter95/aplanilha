<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\DocumentTemplateController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/

/*
|--------------------------------------------------------------------------
| Guest Routes (Authentication)
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| Authenticated Routes - Dashboard
|--------------------------------------------------------------------------
*/
Route::group([], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes - User Management
|--------------------------------------------------------------------------
*/
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes - Role Management
|--------------------------------------------------------------------------
*/
Route::prefix('roles')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('roles');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes - Projects
|--------------------------------------------------------------------------
*/
Route::prefix('projects')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->name('projects');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes - Settings
|--------------------------------------------------------------------------
*/
Route::prefix('settings')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('settings');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes - Document Templates
|--------------------------------------------------------------------------
*/
Route::prefix('document-templates')->group(function () {
    Route::get('/', [DocumentTemplateController::class, 'index'])->name('document-templates');
    Route::get('/new/{tempId}', [DocumentTemplateController::class, 'create'])->name('document-templates.new');
    Route::get('/{id}/edit', [DocumentTemplateController::class, 'edit'])->name('document-templates.edit');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes - Document Types
|--------------------------------------------------------------------------
*/
Route::prefix('document-types')->group(function () {
    Route::get('/', [DocumentTypeController::class, 'index'])->name('document-types');
    Route::get('/new/{tempId}', [DocumentTypeController::class, 'create'])->name('document-types.new');
    Route::get('/{id}/edit', [DocumentTypeController::class, 'edit'])->name('document-types.edit');
});

/*
|--------------------------------------------------------------------------
| Logout Route
|--------------------------------------------------------------------------
*/
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');