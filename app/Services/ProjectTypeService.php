<?php

namespace App\Services;

use App\Models\ProjectType;
use App\Repositories\Interfaces\ProjectTypeRepositoryInterface;
use App\Services\Interfaces\ProjectTypeServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class ProjectTypeService implements ProjectTypeServiceInterface
{
    protected ProjectTypeRepositoryInterface $projectTypeRepository;

    public function __construct(ProjectTypeRepositoryInterface $projectTypeRepository)
    {
        $this->projectTypeRepository = $projectTypeRepository;
    }

    public function getProjectTypesByClient(string $clientId, int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->projectTypeRepository->getByClientPaginated($clientId, $perPage, $filters);
    }

    public function getAllProjectTypesByClient(string $clientId): Collection
    {
        return $this->projectTypeRepository->getAllByClient($clientId);
    }

    public function create(array $data): object
    {
        // Validar se já existe um tipo com o mesmo título para o cliente
        if ($this->projectTypeRepository->titleExistsForClient($data['title'], $data['client_id'])) {
            throw new Exception('Já existe um tipo de projeto com este título.');
        }

        // Define status padrão como ativo se não informado
        $data['status'] = $data['status'] ?? 'a';
        
        // Define cor padrão se não informada
        $data['color'] = $data['color'] ?? '#000000';

        return $this->projectTypeRepository->create($data);
    }

    public function update(string $id, array $data): bool
    {
        $projectType = $this->projectTypeRepository->find($id);
        
        if (!$projectType) {
            throw new Exception('Tipo de projeto não encontrado.');
        }

        // Validar título único para o cliente (exceto o próprio registro)
        if (isset($data['title']) && 
            $this->projectTypeRepository->titleExistsForClient($data['title'], $projectType->client_id, $id)) {
            throw new Exception('Já existe um tipo de projeto com este título.');
        }

        return $this->projectTypeRepository->update($id, $data);
    }

    public function delete(string $id): bool
    {
        $projectType = $this->projectTypeRepository->find($id);
        
        if (!$projectType) {
            throw new Exception('Tipo de projeto não encontrado.');
        }

        // Verificar se existem projetos vinculados
        if ($projectType->projects()->count() > 0) {
            throw new Exception('Não é possível excluir este tipo de projeto pois existem projetos vinculados a ele.');
        }

        return $this->projectTypeRepository->delete($id);
    }

    public function findById(string $id): ?object
    {
        return $this->projectTypeRepository->find($id);
    }

    public function activate(string $id): bool
    {
        $projectType = $this->projectTypeRepository->find($id);
        
        if (!$projectType) {
            throw new Exception('Tipo de projeto não encontrado.');
        }

        return $this->projectTypeRepository->update($id, ['status' => 'a']);
    }

    public function block(string $id): bool
    {
        $projectType = $this->projectTypeRepository->find($id);
        
        if (!$projectType) {
            throw new Exception('Tipo de projeto não encontrado.');
        }

        return $this->projectTypeRepository->update($id, ['status' => 'b']);
    }

    public function exportToCsv(string $clientId, array $filters = []): string
    {
        $projectTypes = $this->projectTypeRepository->getAllByClient($clientId, $filters);
        
        $csvData = "ID,Título,Cor,Status,Criado em\n";
        
        foreach ($projectTypes as $type) {
            $status = $type->status === 'a' ? 'Ativo' : 'Bloqueado';
            $csvData .= sprintf(
                "%s,%s,%s,%s,%s\n",
                $type->id,
                $type->title,
                $type->color,
                $status,
                $type->created_at->format('d/m/Y H:i:s')
            );
        }
        
        return $csvData;
    }
}
