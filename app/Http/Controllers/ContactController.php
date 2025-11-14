<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\ContactServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function __construct(private ContactServiceInterface $service)
    {
    }

    public function index(Request $request): Response|RedirectResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $filters = [
                'search' => $request->get('search'),
                'type' => $request->get('type'),
                'city' => $request->get('city'),
                'country' => $request->get('country'),
                'responsible_user_id' => $request->get('responsible_user_id'),
                'sort_by' => $request->get('sort_by'),
                'sort_direction' => $request->get('sort_direction', 'asc'),
                'tag' => $request->get('tag'),
                'view' => $request->get('view'),
                // Filtros avançados
                'has_email' => $request->boolean('has_email'),
                'has_phone' => $request->boolean('has_phone'),
                'created_recently' => $request->boolean('created_recently'),
                'no_location' => $request->boolean('no_location'),
            ];

            $contacts = $this->service->getAllPaginated($perPage, $filters);

            return Inertia::render('Contacts/Index', [
                'contacts' => $contacts,
                'filters' => $filters,
                'user' => Auth::user(),
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar contatos: ' . $e->getMessage());
        }
    }

    public function create(Request $request, $tempKey = null): Response|RedirectResponse
    {
        if (!$tempKey) {
            $tempKey = 'temp-' . time() . '-' . rand(1000, 9999);
            return redirect()->route('contacts.create', ['tempKey' => $tempKey]);
        }

        return Inertia::render('Contacts/Form', [
            'mode' => 'create',
            'tempKey' => $tempKey,
            'contact' => null,
            'user' => Auth::user(),
        ]);
    }

    public function edit(string $id): Response|RedirectResponse
    {
        try {
            $contact = $this->service->find($id);

            if (!$contact) {
                return redirect()->route('contacts.index')->with('error', 'Contato não encontrado.');
            }

            return Inertia::render('Contacts/Form', [
                'mode' => 'edit',
                'tempKey' => null,
                'contact' => $contact,
                'user' => Auth::user(),
            ]);
        } catch (\Exception $e) {
            return redirect()->route('contacts.index')->with('error', 'Erro ao carregar contato: ' . $e->getMessage());
        }
    }
}
