<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): Response
    {
        $user = Auth::user();
        $company = null;
        
        if ($user && $user->client_id) {
            $company = \App\Models\ClientSubscribe::find($user->client_id);
        }
        
        return Inertia::render('Settings', [
            'company' => $company
        ]);
    }
}
