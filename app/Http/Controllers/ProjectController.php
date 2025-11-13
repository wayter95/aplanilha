<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\DocumentTypeServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Exception;

class ProjectController extends Controller
{
    public function __construct(private DocumentTypeServiceInterface $service)
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return Inertia::render('Projects/Index');
    }
}