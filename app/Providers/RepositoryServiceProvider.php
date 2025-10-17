<?php

namespace App\Providers;

use App\Repositories\ClientSubscribeRepository;
use App\Repositories\UserRepository;
use App\Repositories\Interfaces\ClientSubscribeRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\ClientSubscribeService;
use App\Services\UserService;
use App\Services\Interfaces\ClientSubscribeServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ClientSubscribeRepositoryInterface::class, ClientSubscribeRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        
        $this->app->bind(ClientSubscribeServiceInterface::class, ClientSubscribeService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
    }

    public function boot(): void
    {
        //
    }
}
