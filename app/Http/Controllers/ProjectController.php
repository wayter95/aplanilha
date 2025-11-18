<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\ProjectServiceInterface;
use App\Services\Interfaces\ProjectTypeServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Exception;

class ProjectController extends Controller
{
    public function __construct(
        private ProjectServiceInterface $service,
        private ProjectTypeServiceInterface $projectTypeService
    ) {
        $this->middleware('auth');
    }

    public function index(Request $request): Response
    {
        $user = Auth::user();
        $clientId = $user->client_id;

        $filters = [
            'search' => $request->input('search', ''),
            'status' => $request->input('status', ''),
            'project_type' => $request->input('project_type', ''),
        ];

        $projects = $this->service->getProjectsByClient($clientId, 15, $filters);
        $projectTypes = $this->projectTypeService->getAllProjectTypesByClient($clientId);

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'projectTypes' => $projectTypes,
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        $user = Auth::user();
        $clientId = $user->client_id;

        $projectTypes = $this->projectTypeService->getAllProjectTypesByClient($clientId);

        return Inertia::render('Projects/Form', [
            'project' => null,
            'projectTypes' => $projectTypes,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $user = Auth::user();
            
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'project_number' => 'nullable|string|max:100',
                'uf_project' => 'nullable|string|max:2',
                'project_types_id' => 'required|uuid|exists:project_types,id',
                'responsible_user_id' => 'nullable|uuid|exists:users,id',
                'user_manager_id' => 'nullable|uuid|exists:users,id',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'status' => 'nullable|in:active,pending,cancelled,completed',
            ]);

            $data['client_id'] = $user->client_id;

            $this->service->create($data);

            return redirect()->route('projects.index')
                ->with('success', 'Projeto criado com sucesso!');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function edit(string $id): Response
    {
        $user = Auth::user();
        $clientId = $user->client_id;

        $project = $this->service->findById($id);

        if (!$project) {
            abort(404, 'Projeto não encontrado');
        }

        // Carregar relacionamentos
        $project->load(['projectType', 'responsibleUser', 'managerUser', 'clientContact', 'locationContact']);

        $projectTypes = $this->projectTypeService->getAllProjectTypesByClient($clientId);

        return Inertia::render('Projects/Form', [
            'project' => $project,
            'projectTypes' => $projectTypes,
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'project_number' => 'nullable|string|max:100',
                'uf_project' => 'nullable|string|max:2',
                'project_types_id' => 'required|uuid|exists:project_types,id',
                'responsible_user_id' => 'nullable|uuid|exists:users,id',
                'user_manager_id' => 'nullable|uuid|exists:users,id',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'status' => 'nullable|in:active,pending,cancelled,completed',
            ]);

            $this->service->update($id, $data);

            return redirect()->route('projects.index')
                ->with('success', 'Projeto atualizado com sucesso!');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->service->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Projeto excluído com sucesso!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function activate(string $id): JsonResponse
    {
        try {
            $this->service->activate($id);

            return response()->json([
                'success' => true,
                'message' => 'Projeto ativado com sucesso!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function cancel(string $id): JsonResponse
    {
        try {
            $this->service->cancel($id);

            return response()->json([
                'success' => true,
                'message' => 'Projeto cancelado com sucesso!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function complete(string $id): JsonResponse
    {
        try {
            $this->service->complete($id);

            return response()->json([
                'success' => true,
                'message' => 'Projeto concluído com sucesso!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}