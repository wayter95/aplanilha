<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Repositories
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Interfaces\DocumentTypeRepositoryInterface;
use App\Repositories\Interfaces\DocumentTemplateRepositoryInterface;
use App\Repositories\Interfaces\ClientSubscribeRepositoryInterface;
use App\Repositories\Interfaces\PasswordResetTokenRepositoryInterface;
use App\Repositories\Interfaces\ProjectTypeRepositoryInterface;

use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use App\Repositories\DocumentTypeRepository;
use App\Repositories\DocumentTemplateRepository;
use App\Repositories\ClientSubscribeRepository;
use App\Repositories\PasswordResetTokenRepository;
use App\Repositories\ProjectTypeRepository;

// Services
use App\Services\Interfaces\UserServiceInterface;
use App\Services\Interfaces\RoleServiceInterface;
use App\Services\Interfaces\DocumentTypeServiceInterface;
use App\Services\Interfaces\DocumentTemplateServiceInterface;
use App\Services\Interfaces\DocumentPdfServiceInterface;
use App\Services\Interfaces\DocumentRenderServiceInterface;
use App\Services\Interfaces\ClientSubscribeServiceInterface;
use App\Services\Interfaces\PasswordResetTokenServiceInterface;
use App\Services\Interfaces\ProjectTypeServiceInterface;

use App\Services\UserService;
use App\Services\RoleService;
use App\Services\DocumentTypeService;
use App\Services\DocumentTemplateService;
use App\Services\DocumentPdfService;
use App\Services\DocumentRenderService;
use App\Services\ClientSubscribeService;
use App\Services\PasswordResetTokenService;
use App\Services\ProjectTypeService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository Bindings
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(DocumentTypeRepositoryInterface::class, DocumentTypeRepository::class);
        $this->app->bind(DocumentTemplateRepositoryInterface::class, DocumentTemplateRepository::class);
        $this->app->bind(ClientSubscribeRepositoryInterface::class, ClientSubscribeRepository::class);
        $this->app->bind(PasswordResetTokenRepositoryInterface::class, PasswordResetTokenRepository::class);
        $this->app->bind(ProjectTypeRepositoryInterface::class, ProjectTypeRepository::class);

        // Service Bindings
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(RoleServiceInterface::class, RoleService::class);
        $this->app->bind(DocumentTypeServiceInterface::class, DocumentTypeService::class);
        $this->app->bind(DocumentTemplateServiceInterface::class, DocumentTemplateService::class);
        $this->app->bind(DocumentPdfServiceInterface::class, DocumentPdfService::class);
        $this->app->bind(DocumentRenderServiceInterface::class, DocumentRenderService::class);
        $this->app->bind(ClientSubscribeServiceInterface::class, ClientSubscribeService::class);
        $this->app->bind(PasswordResetTokenServiceInterface::class, PasswordResetTokenService::class);
        $this->app->bind(ProjectTypeServiceInterface::class, ProjectTypeService::class);
    }

    public function boot(): void
    {
        //
    }
}
