<?php

namespace App\Services;

use App\Models\Contact;
use App\Repositories\Interfaces\ContactRepositoryInterface;
use App\Services\Interfaces\ContactServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ContactService implements ContactServiceInterface
{
    public function __construct(private ContactRepositoryInterface $repository)
    {
    }

    public function create(array $data): Model
    {
        $data['id'] = $data['id'] ?? Str::uuid()->toString();
        
        $tenantContext = app('tenant.context');
        $clientId = $tenantContext ? $tenantContext->getClientId() : null;
        
        if (!$clientId && Auth::check()) {
            $user = Auth::user();
            $clientId = $user->client_id ?? null;
        }
        
        $data['client_id'] = $data['client_id'] ?? $clientId;
        
        if (!isset($data['responsible_user_id']) && Auth::check()) {
            $data['responsible_user_id'] = Auth::id();
        }

        // Separar dados das pessoas de contato
        $contactPersonsData = $data['contact_persons'] ?? [];
        unset($data['contact_persons']);

        // Criar o contato principal
        $contact = $this->repository->create($data);

        // Criar as pessoas de contato se existirem
        if (!empty($contactPersonsData)) {
            $this->createContactPersons($contact, $contactPersonsData);
        }

        return $contact;
    }

    public function update(string $id, array $data): bool
    {
        // Separar dados das pessoas de contato
        $contactPersonsData = $data['contact_persons'] ?? null;
        unset($data['contact_persons']);

        // Atualizar o contato principal
        $success = $this->repository->update($id, $data);

        if ($success && $contactPersonsData !== null) {
            $contact = $this->find($id);
            if ($contact) {
                // Remover pessoas de contato existentes
                $contact->contactPersons()->delete();
                
                // Criar as novas pessoas de contato
                if (!empty($contactPersonsData)) {
                    $this->createContactPersons($contact, $contactPersonsData);
                }
            }
        }

        return $success;
    }

    private function createContactPersons(Model $contact, array $contactPersonsData): void
    {
        foreach ($contactPersonsData as $personData) {
            // Criar a pessoa de contato
            $contactPerson = $contact->contactPersons()->create([
                'id' => Str::uuid()->toString(),
                'first_name' => $personData['first_name'],
                'last_name' => $personData['last_name'] ?? null,
                'mobile' => $personData['mobile'] ?? null,
                'role' => $personData['role'] ?? null,
            ]);

            // Criar os e-mails da pessoa
            if (!empty($personData['emails'])) {
                foreach ($personData['emails'] as $email) {
                    if (!empty($email)) {
                        $contactPerson->emails()->create([
                            'id' => Str::uuid()->toString(),
                            'email' => $email,
                        ]);
                    }
                }
            }

            // Criar as notas da pessoa
            if (!empty($personData['notes'])) {
                foreach ($personData['notes'] as $noteData) {
                    if (!empty($noteData['name'])) {
                        $contactPerson->notes()->create([
                            'id' => Str::uuid()->toString(),
                            'name' => $noteData['name'],
                            'content' => $noteData['content'] ?? null,
                            'note_date' => $noteData['note_date'] ?? now(),
                            'created_by' => Auth::id(),
                        ]);
                    }
                }
            }
        }
    }

    public function delete(string $id): bool
    {
        return $this->repository->delete($id);
    }

    public function find(string $id): ?Model
    {
        return $this->repository->find($id);
    }

    public function getAllPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($perPage, $filters);
    }

    public function findByType(string $type): Collection
    {
        return $this->repository->findByType($type);
    }

    public function findByCity(string $city): Collection
    {
        return $this->repository->findByCity($city);
    }

    public function findByCountry(string $country): Collection
    {
        return $this->repository->findByCountry($country);
    }

    public function searchContacts(string $search, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->searchContacts($search, $perPage);
    }

    public function getAvailableCities(): array
    {
        $cities = Contact::selectRaw('COALESCE(city_visiting, city_mailing, city_billing) as city')
            ->whereNotNull('city_visiting')
            ->orWhereNotNull('city_mailing')
            ->orWhereNotNull('city_billing')
            ->distinct()
            ->pluck('city')
            ->filter()
            ->sort()
            ->values()
            ->toArray();

        return $cities;
    }

    public function getAvailableCountries(): array
    {
        $countries = Contact::selectRaw('COALESCE(country_visiting, country_mailing, country_billing) as country')
            ->whereNotNull('country_visiting')
            ->orWhereNotNull('country_mailing')
            ->orWhereNotNull('country_billing')
            ->distinct()
            ->pluck('country')
            ->filter()
            ->sort()
            ->values()
            ->toArray();

        return $countries;
    }
}
