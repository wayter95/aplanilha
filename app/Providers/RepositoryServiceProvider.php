<?php

namespace App\Providers;

use App\Repositories\ClientSubscribeRepository;
use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use App\Repositories\DocumentTemplateRepository;
use App\Repositories\DocumentTypeRepository;
use App\Repositories\Interfaces\ClientSubscribeRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Interfaces\DocumentTemplateRepositoryInterface;
use App\Repositories\Interfaces\DocumentTypeRepositoryInterface;
use App\Services\ClientSubscribeService;
use App\Services\UserService;
use App\Services\RoleService;
use App\Services\DocumentTemplateService;
use App\Services\DocumentTypeService;
use App\Services\DocumentRenderService;
use App\Services\DocumentPdfService;
use App\Services\Interfaces\ClientSubscribeServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\Interfaces\RoleServiceInterface;
use App\Services\Interfaces\DocumentTemplateServiceInterface;
use App\Services\Interfaces\DocumentTypeServiceInterface;
use App\Services\Interfaces\DocumentRenderServiceInterface;
use App\Services\Interfaces\DocumentPdfServiceInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository Bindings
        $this->app->bind(ClientSubscribeRepositoryInterface::class, ClientSubscribeRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(DocumentTemplateRepositoryInterface::class, DocumentTemplateRepository::class);
        $this->app->bind(DocumentTypeRepositoryInterface::class, DocumentTypeRepository::class);
        
        // Service Bindings
        $this->app->bind(ClientSubscribeServiceInterface::class, ClientSubscribeService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(RoleServiceInterface::class, RoleService::class);
        $this->app->bind(DocumentTemplateServiceInterface::class, DocumentTemplateService::class);
        $this->app->bind(DocumentTypeServiceInterface::class, DocumentTypeService::class);
        $this->app->bind(DocumentRenderServiceInterface::class, DocumentRenderService::class);
        $this->app->bind(DocumentPdfServiceInterface::class, DocumentPdfService::class);
    }

    public function boot(): void
    {
        //
    }
}
