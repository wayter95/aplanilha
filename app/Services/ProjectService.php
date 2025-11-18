<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Services\Interfaces\ProjectServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class ProjectService implements ProjectServiceInterface
{
    protected ProjectRepositoryInterface $projectRepository;

    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function getProjectsByClient(string $clientId, int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->projectRepository->getByClientPaginated($clientId, $perPage, $filters);
    }

    public function getAllProjectsByClient(string $clientId): Collection
    {
        return $this->projectRepository->getAllByClient($clientId);
    }

    public function create(array $data): object
    {
        // Validar se já existe um projeto com o mesmo número para o cliente
        if (isset($data['project_number']) && $this->projectRepository->projectNumberExistsForClient($data['project_number'], $data['client_id'])) {
            throw new Exception('Já existe um projeto com este número.');
        }

        // Define status padrão como pending se não informado
        $data['status'] = $data['status'] ?? 'pending';

        return $this->projectRepository->create($data);
    }

    public function update(string $id, array $data): bool
    {
        $project = $this->projectRepository->find($id);
        
        if (!$project) {
            throw new Exception('Projeto não encontrado.');
        }

        // Validar número único para o cliente (exceto o próprio registro)
        if (isset($data['project_number']) && 
            $this->projectRepository->projectNumberExistsForClient($data['project_number'], $project->client_id, $id)) {
            throw new Exception('Já existe um projeto com este número.');
        }

        return $this->projectRepository->update($id, $data);
    }

    public function delete(string $id): bool
    {
        $project = $this->projectRepository->find($id);
        
        if (!$project) {
            throw new Exception('Projeto não encontrado.');
        }

        return $this->projectRepository->delete($id);
    }

    public function findById(string $id): ?object
    {
        return $this->projectRepository->find($id);
    }

    public function activate(string $id): bool
    {
        $project = $this->projectRepository->find($id);
        
        if (!$project) {
            throw new Exception('Projeto não encontrado.');
        }

        return $this->projectRepository->update($id, ['status' => 'active']);
    }

    public function cancel(string $id): bool
    {
        $project = $this->projectRepository->find($id);
        
        if (!$project) {
            throw new Exception('Projeto não encontrado.');
        }

        return $this->projectRepository->update($id, ['status' => 'cancelled']);
    }

    public function complete(string $id): bool
    {
        $project = $this->projectRepository->find($id);
        
        if (!$project) {
            throw new Exception('Projeto não encontrado.');
        }

        return $this->projectRepository->update($id, ['status' => 'completed']);
    }

    public function getByProjectType(string $projectTypeId): Collection
    {
        return $this->projectRepository->getByProjectType($projectTypeId);
    }

    public function getByStatus(string $clientId, string $status): Collection
    {
        return $this->projectRepository->getByStatus($clientId, $status);
    }
}
