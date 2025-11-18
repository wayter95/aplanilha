<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\ContactServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function __construct(private ContactServiceInterface $service)
    {
        $this->middleware('auth');
    }

    /**
     * Buscar contatos para Select2
     * Usado para autocomplete em formulários
     */
    public function searchForSelect2(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $clientId = $user->client_id;

            $search = $request->input('search', '');
            $type = $request->input('type', null);
            $limit = $request->input('limit', 10);

            $contacts = $this->service->searchForSelect2($clientId, $search, $type, $limit);

            return response()->json([
                'success' => true,
                'data' => $contacts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Obter todos os clientes (tipo customer)
     */
    public function getCustomers(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $clientId = $user->client_id;

            $customers = $this->service->getCustomers($clientId);

            return response()->json([
                'success' => true,
                'data' => $customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Obter todos os locais (tipo location)
     */
    public function getLocations(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $clientId = $user->client_id;

            $locations = $this->service->getLocations($clientId);

            return response()->json([
                'success' => true,
                'data' => $locations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Obter contato por ID
     */
    public function show(string $id): JsonResponse
    {
        try {
            $contact = $this->service->findById($id);

            if (!$contact) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contato não encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $contact
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
