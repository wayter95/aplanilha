<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\DocumentTemplateController;
use App\Http\Controllers\Api\UserSettingsController;
use App\Http\Controllers\Api\CompanySettingsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    
    Route::get('users', [UserController::class, 'index'])->name('users');
    
    Route::get('roles', [RoleController::class, 'index'])->name('roles');
    
    Route::get('settings', function () {
        $user = Auth::user();
        $company = null;
        
        if ($user && $user->client_id) {
            $company = \App\Models\ClientSubscribe::find($user->client_id);
        }
        
        return Inertia::render('Settings', [
            'user' => $user,
            'company' => $company
        ]);
    })->name('settings');
    Route::get('document-templates', function () {
        return Inertia::render('DocumentTemplates/Index', [
            'user' => Auth::user(),
        ]);
    })->name('document-templates');
    
    // Rotas para criação/edição com IDs na URL (inclui tempId para novas abas)
    Route::get('document-templates/new/{tempId}', function ($tempId) {
        return Inertia::render('DocumentTemplates/Form', [
            'id' => null,
            'tempKey' => $tempId,
            'user' => Auth::user(),
        ]);
    })->name('document-templates.new');
    Route::get('document-templates/{id}/edit', function ($id) {
        return Inertia::render('DocumentTemplates/Form', [
            'id' => $id,
            'tempKey' => null,
            'user' => Auth::user(),
        ]);
    })->name('document-templates.edit');
    
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
    
    Route::prefix('api/users')->group(function () {
        Route::patch('/{id}/photo', [UserController::class, 'updatePhoto'])->name('users.update-photo');
    });
    
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
    
    Route::prefix('api/files')->group(function () {
        Route::post('/presigned-url', [FileUploadController::class, 'generatePresignedUrl'])->name('files.presigned-url');
        Route::post('/temporary-url', [FileUploadController::class, 'generateTemporaryUrl'])->name('files.temporary-url');
        Route::get('/signed-url', [FileUploadController::class, 'getSignedUrl'])->name('uploads.signed-url');
        Route::delete('/delete', [FileUploadController::class, 'deleteFile'])->name('files.delete');
    });
    
    Route::prefix('api/document-templates')->group(function () {
        Route::get('/types', [DocumentTemplateController::class, 'types'])->name('document-templates.types');
        Route::get('/', [DocumentTemplateController::class, 'index'])->name('document-templates.index');
        Route::post('/', [DocumentTemplateController::class, 'store'])->name('document-templates.store');
        Route::get('/placeholders', [DocumentTemplateController::class, 'placeholders'])->name('document-templates.placeholders');
        Route::get('/{id}', [DocumentTemplateController::class, 'show'])->name('document-templates.show');
        Route::put('/{id}', [DocumentTemplateController::class, 'update'])->name('document-templates.update');
        Route::delete('/{id}', [DocumentTemplateController::class, 'destroy'])->name('document-templates.destroy');
        Route::post('/{id}/set-default', [DocumentTemplateController::class, 'setDefault'])->name('document-templates.set-default');
    });

    Route::prefix('api/document-generation')->group(function () {
        Route::post('/{id}/preview-html', [\App\Http\Controllers\Api\DocumentGenerationController::class, 'previewHtml'])->name('document-generation.preview-html');
        Route::get('/{id}/export-pdf', [\App\Http\Controllers\Api\DocumentGenerationController::class, 'exportPdf'])->name('document-generation.export-pdf');
    });
    
    // Rotas para configurações do usuário
    Route::prefix('api/user')->group(function () {
        Route::put('/personal-data', [UserSettingsController::class, 'updatePersonalData'])->name('user.personal-data');
        Route::put('/password', [UserSettingsController::class, 'updatePassword'])->name('user.password');
    });
    
    // Rotas para dados da empresa
    Route::prefix('api/company')->group(function () {
        Route::put('/data', [CompanySettingsController::class, 'updateData'])->name('company.data');
    });
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware('guest')->group(function () {
    Route::get('/sign-in', [AuthController::class, 'showLogin'])->name('signin');
    Route::get('/login', fn() => redirect()->route('signin'))->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
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