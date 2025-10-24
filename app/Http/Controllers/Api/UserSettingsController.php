<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserSettingsController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Atualizar dados pessoais do usuário
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
     * Atualizar senha do usuário
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
                'message' => 'Erro ao alterar senha: ' . $e->getMessage(),
            ], 500);
        }
    }
}
