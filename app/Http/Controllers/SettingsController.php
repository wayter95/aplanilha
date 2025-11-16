<?php

namespace App\Http\Controllers;

use App\Models\ClientSubscribe;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function __construct(private ?UserServiceInterface $userService = null)
    {
        $this->middleware('auth');
    }

    public function index(): Response
    {
        $user = Auth::user();
        $company = null;
        
        if ($user && $user->client_id) {
            $company = ClientSubscribe::find($user->client_id);
        }
        
        return Inertia::render('Settings', [
            'company' => $company
        ]);
    }

    // ==================== API JSON Methods ====================

    /**
     * API: Atualizar dados pessoais do usuário
     */
    public function updatePersonalData(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'nullable|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
            ]);

            $updatedUser = $this->userService->updateUser($user->id, $validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Dados pessoais atualizados com sucesso!',
                'user' => $updatedUser,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar dados pessoais: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Atualizar senha do usuário
     */
    public function updatePassword(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $validatedData = $request->validate([
                'current_password' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required|string|min:8',
            ]);

            // Verificar se a senha atual está correta
            if (!Hash::check($validatedData['current_password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Senha atual incorreta',
                ], 422);
            }

            // Atualizar a senha
            $passwordData = [
                'password' => $validatedData['password']
            ];

            $this->userService->updateUser($user->id, $passwordData);

            return response()->json([
                'success' => true,
                'message' => 'Senha alterada com sucesso!',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar senha: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Atualizar dados da empresa
     */
    public function updateCompanyData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            // Buscar o cliente/empresa do usuário
            $client = ClientSubscribe::find($user->client_id);
            
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Empresa não encontrada'
                ], 404);
            }

            // Atualizar dados da empresa
            $client->update([
                'name' => $request->name,
                'email' => $request->email,
                'cnpj' => $request->cnpj,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dados da empresa atualizados com sucesso',
                'company' => [
                    'name' => $client->name,
                    'cnpj' => $client->cnpj,
                    'email' => $client->email,
                    'phone' => $client->phone,
                    'address' => $client->address,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }
}
