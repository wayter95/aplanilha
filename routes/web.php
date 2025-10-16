<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render(component: 'Home');
});

Route::get('/sign-in', function () {
    return Inertia::render(component: 'Signin');
});