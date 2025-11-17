<?php

namespace App\Services;

use App\Repositories\Interfaces\DocumentTypeRepositoryInterface;
use App\Services\Interfaces\DocumentTypeServiceInterface;
use App\Models\DocumentTemplate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocumentTypeService implements DocumentTypeServiceInterface
{
    public function __construct(private DocumentTypeRepositoryInterface $repository)
    {
    }

    public function create(array $data): Model
    {
        $data['id'] = $data['id'] ?? Str::uuid()->toString();
        
        // client_id deve vir do controller (via middleware)
        // Se não vier, pega do usuário autenticado
        if (!isset($data['client_id'])) {
            $data['client_id'] = auth()->user()?->client_id;
        }
        
        if (empty($data['code'])) {
            $data['code'] = Str::slug($data['name']);
        }
        
        $data['code'] = strtolower($data['code']);

        return $this->repository->create($data);
    }

    public function update(string $id, array $data): bool
    {
        if (isset($data['code']) && empty($data['code'])) {
            unset($data['code']);
        }
        
        return $this->repository->update($id, $data);
    }

    public function delete(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $type = $this->repository->find($id);
            if (!$type) {
                return false;
            }

            $hasTemplates = DocumentTemplate::where('type', $type->code)
                ->exists();

            if ($hasTemplates) {
                abort(422, 'Não é possível excluir um tipo que possui templates associados');
            }

            return $this->repository->delete($id);
        });
    }

    public function find(string $id): ?Model
    {
        return $this->repository->find($id);
    }

    public function findActive(): Collection
    {
        return $this->repository->findActive();
    }

    public function findByCode(string $code): ?Model
    {
        return $this->repository->findByCode($code);
    }

    public function getCodes(): array
    {
        return $this->repository->getCodes();
    }

    public function validateType(string $code): bool
    {
        $type = $this->repository->findByCode($code);
        return $type !== null && $type->is_active;
    }

    public function getAllPaginated(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($perPage, $filters);
    }

    public function getByClientPaginated(string $clientId, int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->getByClientPaginated($clientId, $perPage, $filters);
    }

    public function getAll(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }
}

