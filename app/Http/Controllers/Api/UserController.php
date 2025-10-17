<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        private UserServiceInterface $userService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $clientId = app('tenant.context')->getClientId();
        $users = $this->userService->getUsersByClient($clientId);
        return response()->json($users);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,NULL,id,client_id,' . app('tenant.context')->getClientId(),
            'password' => 'required|string|min:8',
            'avatar_path' => 'nullable|string|max:255',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:user_roles,id',
        ]);

        $validated['client_id'] = app('tenant.context')->getClientId();
        
        $user = $this->userService->createUser($validated);
        
        if (isset($validated['roles'])) {
            foreach ($validated['roles'] as $roleId) {
                $this->userService->assignRoleToUser($user->id, $roleId);
            }
        }
        
        return response()->json($user, 201);
    }

    public function show(string $id): JsonResponse
    {
        $user = $this->userService->getUser($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id . ',id,client_id,' . app('tenant.context')->getClientId(),
            'password' => 'sometimes|string|min:8',
            'avatar_path' => 'nullable|string|max:255',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:user_roles,id',
        ]);

        try {
            $user = $this->userService->updateUser($id, $validated);
            
            if (isset($validated['roles'])) {
                $user->roles()->sync($validated['roles']);
            }
            
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->userService->deleteUser($id);
        if (!$deleted) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function assignRole(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:user_roles,id',
        ]);

        $assigned = $this->userService->assignRoleToUser($id, $validated['role_id']);
        if (!$assigned) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        return response()->json(['message' => 'Role assigned successfully']);
    }

    public function removeRole(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:user_roles,id',
        ]);

        $removed = $this->userService->removeRoleFromUser($id, $validated['role_id']);
        if (!$removed) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        return response()->json(['message' => 'Role removed successfully']);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $reset = $this->userService->resetUserPassword(
            $validated['email'], 
            app('tenant.context')->getClientId()
        );
        
        if (!$reset) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        return response()->json(['message' => 'Password reset link sent']);
    }
}
