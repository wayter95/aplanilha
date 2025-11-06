<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\ClientSubscribeServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientSubscribeController extends Controller
{
    public function __construct(
        private ClientSubscribeServiceInterface $clientService
    ) {}

    public function index(): JsonResponse
    {
        $clients = $this->clientService->getAllClients();
        return response()->json($clients);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:255|unique:client_subscribes',
            'email' => 'required|email|unique:client_subscribes',
            'cnpj' => 'required|string|unique:client_subscribes',
            'plan' => 'nullable|string|max:100',
            'expires_at' => 'nullable|date',
        ]);

        $client = $this->clientService->createClient($validated);
        return response()->json($client, 201);
    }

    public function show(string $id): JsonResponse
    {
        $client = $this->clientService->getClient($id);
        if (!$client) {
            return response()->json(['message' => 'Client not found'], 404);
        }
        return response()->json($client);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'subdomain' => 'sometimes|string|max:255|unique:client_subscribes,subdomain,' . $id,
            'email' => 'sometimes|email|unique:client_subscribes,email,' . $id,
            'cnpj' => 'sometimes|string|unique:client_subscribes,cnpj,' . $id,
            'plan' => 'nullable|string|max:100',
            'active' => 'sometimes|boolean',
            'expires_at' => 'nullable|date',
        ]);

        try {
            $client = $this->clientService->updateClient($id, $validated);
            return response()->json($client);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->clientService->deleteClient($id);
        if (!$deleted) {
            return response()->json(['message' => 'Client not found'], 404);
        }
        return response()->json(['message' => 'Client deleted successfully']);
    }

    public function activate(string $id): JsonResponse
    {
        $activated = $this->clientService->activateClient($id);
        if (!$activated) {
            return response()->json(['message' => 'Client not found'], 404);
        }
        return response()->json(['message' => 'Client activated successfully']);
    }

    public function deactivate(string $id): JsonResponse
    {
        $deactivated = $this->clientService->deactivateClient($id);
        if (!$deactivated) {
            return response()->json(['message' => 'Client not found'], 404);
        }
        return response()->json(['message' => 'Client deactivated successfully']);
    }
}
