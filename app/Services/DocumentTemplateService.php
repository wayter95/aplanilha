<?php

namespace App\Services;

use App\Repositories\Interfaces\DocumentTemplateRepositoryInterface;
use App\Services\Interfaces\DocumentTemplateServiceInterface;
use App\Services\Interfaces\DocumentTypeServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocumentTemplateService implements DocumentTemplateServiceInterface
{
    public function __construct(
        private DocumentTemplateRepositoryInterface $repository,
        private DocumentTypeServiceInterface $typeService
    ) {
    }

    public function create(array $data): Model
    {
        $this->validateType($data['type'] ?? null);
        $data['id'] = $data['id'] ?? Str::uuid()->toString();
        return $this->repository->create($data);
    }

    public function update(string $id, array $data): bool
    {
        if (isset($data['type'])) {
            $this->validateType($data['type']);
        }
        return $this->repository->update($id, $data);
    }

    public function delete(string $id): bool
    {
        return $this->repository->delete($id);
    }

    public function find(string $id): ?Model
    {
        return $this->repository->find($id);
    }

    public function listByType(string $type, int $perPage = 15): LengthAwarePaginator
    {
        $this->validateType($type);
        return $this->repository->listByType($type, $perPage);
    }

    public function setDefault(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $template = $this->repository->find($id);
            if (!$template) {
                return false;
            }
            if (!$template->client_id) {
                return false;
            }
            $this->repository->unsetDefaultForClientAndType($template->client_id, $template->type);
            return $this->repository->update($id, ['is_default' => true]);
        });
    }

    private function validateType(?string $type): void
    {
        if (!$type || !$this->typeService->validateType($type)) {
            abort(422, 'Invalid document type');
        }
    }
}


