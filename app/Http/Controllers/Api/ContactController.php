<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\ContactServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    public function __construct(private ContactServiceInterface $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $filters = [
            'search' => $request->get('search'),
            'type' => $request->get('type'),
            'city' => $request->get('city'),
            'country' => $request->get('country'),
            'responsible_user_id' => $request->get('responsible_user_id'),
            'sort_by' => $request->get('sort_by'),
            'sort_direction' => $request->get('sort_direction', 'asc'),
        ];

        $contacts = $this->service->getAllPaginated($perPage, $filters);
        return response()->json($contacts);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['customer', 'supplier', 'location'])],
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'name_line' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'responsible_user_id' => 'nullable|exists:users,id',
            
            'street_visiting' => 'nullable|string|max:255',
            'house_number_visiting' => 'nullable|string|max:50',
            'postal_code_visiting' => 'nullable|string|max:20',
            'city_visiting' => 'nullable|string|max:100',
            'state_visiting' => 'nullable|string|max:100',
            'country_visiting' => 'nullable|string|max:100',
            'lat_visiting' => 'nullable|numeric|between:-90,90',
            'lng_visiting' => 'nullable|numeric|between:-180,180',
            
            'street_mailing' => 'nullable|string|max:255',
            'house_number_mailing' => 'nullable|string|max:50',
            'postal_code_mailing' => 'nullable|string|max:20',
            'city_mailing' => 'nullable|string|max:100',
            'state_mailing' => 'nullable|string|max:100',
            'country_mailing' => 'nullable|string|max:100',
            'lat_mailing' => 'nullable|numeric|between:-90,90',
            'lng_mailing' => 'nullable|numeric|between:-180,180',
            
            'street_billing' => 'nullable|string|max:255',
            'house_number_billing' => 'nullable|string|max:50',
            'postal_code_billing' => 'nullable|string|max:20',
            'city_billing' => 'nullable|string|max:100',
            'state_billing' => 'nullable|string|max:100',
            'country_billing' => 'nullable|string|max:100',
            'lat_billing' => 'nullable|numeric|between:-90,90',
            'lng_billing' => 'nullable|numeric|between:-180,180',
        ]);

        try {
            $contact = $this->service->create($validated);
            return response()->json($contact, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao criar contato: ' . $e->getMessage()], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        $contact = $this->service->find($id);

        if (!$contact) {
            return response()->json(['message' => 'Contato não encontrado'], 404);
        }

        return response()->json($contact);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['sometimes', Rule::in(['customer', 'supplier', 'location'])],
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'name_line' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'responsible_user_id' => 'nullable|exists:users,id',
            
            'street_visiting' => 'nullable|string|max:255',
            'house_number_visiting' => 'nullable|string|max:50',
            'postal_code_visiting' => 'nullable|string|max:20',
            'city_visiting' => 'nullable|string|max:100',
            'state_visiting' => 'nullable|string|max:100',
            'country_visiting' => 'nullable|string|max:100',
            'lat_visiting' => 'nullable|numeric|between:-90,90',
            'lng_visiting' => 'nullable|numeric|between:-180,180',
            
            'street_mailing' => 'nullable|string|max:255',
            'house_number_mailing' => 'nullable|string|max:50',
            'postal_code_mailing' => 'nullable|string|max:20',
            'city_mailing' => 'nullable|string|max:100',
            'state_mailing' => 'nullable|string|max:100',
            'country_mailing' => 'nullable|string|max:100',
            'lat_mailing' => 'nullable|numeric|between:-90,90',
            'lng_mailing' => 'nullable|numeric|between:-180,180',
            
            'street_billing' => 'nullable|string|max:255',
            'house_number_billing' => 'nullable|string|max:50',
            'postal_code_billing' => 'nullable|string|max:20',
            'city_billing' => 'nullable|string|max:100',
            'state_billing' => 'nullable|string|max:100',
            'country_billing' => 'nullable|string|max:100',
            'lat_billing' => 'nullable|numeric|between:-90,90',
            'lng_billing' => 'nullable|numeric|between:-180,180',
        ]);

        try {
            $success = $this->service->update($id, $validated);

            if (!$success) {
                return response()->json(['message' => 'Contato não encontrado'], 404);
            }

            $contact = $this->service->find($id);
            return response()->json($contact);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar contato: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $success = $this->service->delete($id);

            if (!$success) {
                return response()->json(['message' => 'Contato não encontrado'], 404);
            }

            return response()->json(['message' => 'Contato excluído com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao excluir contato: ' . $e->getMessage()], 500);
        }
    }

    public function cities(): JsonResponse
    {
        $cities = $this->service->getAvailableCities();
        return response()->json($cities);
    }

    public function countries(): JsonResponse
    {
        $countries = $this->service->getAvailableCountries();
        return response()->json($countries);
    }
}
