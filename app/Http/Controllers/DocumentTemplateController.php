<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DocumentTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): Response
    {
        return Inertia::render('DocumentTemplates/Index');
    }

    public function create(string $tempId): Response
    {
        return Inertia::render('DocumentTemplates/Form', [
            'id' => null,
            'tempKey' => $tempId,
        ]);
    }

    public function edit(string $id): Response
    {
        return Inertia::render('DocumentTemplates/Form', [
            'id' => $id,
            'tempKey' => null,
        ]);
    }
}
