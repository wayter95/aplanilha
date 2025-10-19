<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    
    // Users CRUD routes
    Route::get('users', [UserController::class, 'index'])->name('users');
    
    // Roles CRUD routes
    Route::get('roles', [RoleController::class, 'index'])->name('roles');
    
    // Users API routes
    Route::prefix('api/users')->group(function () {
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::get('/{id}', [UserController::class, 'show'])->name('users.show');
        Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('/statistics', [UserController::class, 'statistics'])->name('users.statistics');
        Route::get('/roles', [UserController::class, 'roles'])->name('users.roles');
        Route::get('/export/csv', [UserController::class, 'exportCsv'])->name('users.export.csv');
    });
    
    // Roles API routes
    Route::prefix('api/roles')->group(function () {
        Route::post('/', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/{id}', [RoleController::class, 'show'])->name('roles.show');
        Route::put('/{id}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::patch('/{id}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status');
        Route::get('/statistics', [RoleController::class, 'statistics'])->name('roles.statistics');
        Route::get('/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
        Route::get('/export/csv', [RoleController::class, 'exportCsv'])->name('roles.export.csv');
    });
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware('guest')->group(function () {
    Route::get('/sign-in', [AuthController::class, 'showLogin'])->name('signin');
    Route::get('/login', fn() => redirect()->route('signin'))->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // Temporary test route for login
    Route::get('/test-login', function () {
        $user = \App\Models\User::where('email', 'joao.silva@empresademo.com')->first();
        if ($user) {
            Auth::guard('web')->login($user);
            return redirect()->route('home');
        }
        return redirect()->route('signin');
    });
    
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});