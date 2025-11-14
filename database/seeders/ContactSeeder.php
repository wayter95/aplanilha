<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\ContactPerson;
use App\Models\ContactPersonEmail;
use App\Models\ContactPersonNote;
use App\Models\ClientSubscribe;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $client = ClientSubscribe::first();
        if (!$client) {
            $this->command->error('Nenhum client encontrado. Execute o seeder de clients primeiro.');
            return;
        }

        $users = User::where('client_id', $client->id)->get();
        if ($users->isEmpty()) {
            $this->command->error('Nenhum usuário encontrado para o client. Execute o seeder de usuários primeiro.');
            return;
        }

        $responsibleUser = $users->first();

        $contacts = [
            [
                'type' => 'customer',
                'name' => 'Tech Solutions Brasil Ltda',
                'email' => 'contato@techsolutions.com.br',
                'phone' => '+55 11 3456-7890',
                'website' => 'https://techsolutions.com.br',
                'city_visiting' => 'São Paulo',
                'state_visiting' => 'SP',
                'country_visiting' => 'Brasil',
                'street_visiting' => 'Av. Paulista',
                'house_number_visiting' => '1500',
                'postal_code_visiting' => '01310-100',
            ],
            [
                'type' => 'customer',
                'name' => 'Empresa ABC Comércio',
                'email' => 'comercial@abc.com',
                'phone' => '+55 21 2345-6789',
                'website' => 'https://empresaabc.com',
                'city_visiting' => 'Rio de Janeiro',
                'state_visiting' => 'RJ',
                'country_visiting' => 'Brasil',
                'street_visiting' => 'Rua do Comércio',
                'house_number_visiting' => '350',
                'postal_code_visiting' => '20040-020',
            ],
            [
                'type' => 'supplier',
                'name' => 'Fornecedor XYZ Distribuidora',
                'email' => 'vendas@xyz.com.br',
                'phone' => '+55 11 4567-8901',
                'website' => 'https://fornecedorxyz.com.br',
                'city_visiting' => 'Guarulhos',
                'state_visiting' => 'SP',
                'country_visiting' => 'Brasil',
                'street_visiting' => 'Rua Industrial',
                'house_number_visiting' => '789',
                'postal_code_visiting' => '07030-000',
            ],
            [
                'type' => 'customer',
                'name' => 'Indústria Metalúrgica Nacional',
                'email' => 'contato@metalurgica.com',
                'phone' => '+55 19 3456-1234',
                'website' => 'https://metalurgica.com',
                'city_visiting' => 'Campinas',
                'state_visiting' => 'SP',
                'country_visiting' => 'Brasil',
                'street_visiting' => 'Av. das Indústrias',
                'house_number_visiting' => '2100',
                'postal_code_visiting' => '13050-000',
            ],
            [
                'type' => 'supplier',
                'name' => 'Importadora Global Trade',
                'email' => 'import@globaltrade.com',
                'phone' => '+55 11 9876-5432',
                'website' => 'https://globaltrade.com',
                'city_visiting' => 'Santos',
                'state_visiting' => 'SP',
                'country_visiting' => 'Brasil',
                'street_visiting' => 'Av. Portuária',
                'house_number_visiting' => '150',
                'postal_code_visiting' => '11010-000',
            ],
            [
                'type' => 'location',
                'name' => 'Depósito Central SP',
                'email' => 'deposito@central.com',
                'phone' => '+55 11 5555-1234',
                'city_visiting' => 'Osasco',
                'state_visiting' => 'SP',
                'country_visiting' => 'Brasil',
                'street_visiting' => 'Rua do Depósito',
                'house_number_visiting' => '500',
                'postal_code_visiting' => '06010-000',
            ],
            [
                'type' => 'customer',
                'name' => 'Construtora Moderna S.A.',
                'email' => 'obras@construtora.com.br',
                'phone' => '+55 31 3210-9876',
                'website' => 'https://construtoramoderna.com.br',
                'city_visiting' => 'Belo Horizonte',
                'state_visiting' => 'MG',
                'country_visiting' => 'Brasil',
                'street_visiting' => 'Av. Afonso Pena',
                'house_number_visiting' => '3500',
                'postal_code_visiting' => '30130-000',
            ],
            [
                'type' => 'supplier',
                'name' => 'Transportadora Rápida Express',
                'email' => 'logistica@rapidaexpress.com',
                'phone' => '+55 41 3344-5566',
                'website' => 'https://rapidaexpress.com',
                'city_visiting' => 'Curitiba',
                'state_visiting' => 'PR',
                'country_visiting' => 'Brasil',
                'street_visiting' => 'Rua da Logística',
                'house_number_visiting' => '1200',
                'postal_code_visiting' => '80010-000',
            ],
            [
                'type' => 'customer',
                'name' => 'Supermercados Família',
                'email' => 'compras@familia.com.br',
                'phone' => '+55 51 3200-1100',
                'website' => 'https://superfamilia.com.br',
                'city_visiting' => 'Porto Alegre',
                'state_visiting' => 'RS',
                'country_visiting' => 'Brasil',
                'street_visiting' => 'Av. dos Estados',
                'house_number_visiting' => '800',
                'postal_code_visiting' => '90010-000',
            ],
            [
                'type' => 'location',
                'name' => 'Armazém Nordeste',
                'email' => 'armazem@nordeste.com',
                'phone' => '+55 81 3100-2200',
                'city_visiting' => 'Recife',
                'state_visiting' => 'PE',
                'country_visiting' => 'Brasil',
                'street_visiting' => 'Rua do Porto',
                'house_number_visiting' => '250',
                'postal_code_visiting' => '50030-000',
            ],
            [
                'type' => 'customer',
                'name' => 'Tecnologia Avançada LTDA',
                'email' => 'suporte@tecavancada.com',
                'phone' => '+55 85 3456-7890',
                'website' => 'https://tecavancada.com',
                'city_visiting' => 'Fortaleza',
                'state_visiting' => 'CE',
                'country_visiting' => 'Brasil',
                'street_visiting' => 'Av. Beira Mar',
                'house_number_visiting' => '1800',
                'postal_code_visiting' => '60165-000',
            ],
            [
                'type' => 'supplier',
                'name' => 'Materiais de Construção Beta',
                'email' => 'vendas@beta.com.br',
                'phone' => '+55 61 3210-5432',
                'website' => 'https://materialbet a.com.br',
                'city_visiting' => 'Brasília',
                'state_visiting' => 'DF',
                'country_visiting' => 'Brasil',
                'street_visiting' => 'SIA Trecho 3',
                'house_number_visiting' => '450',
                'postal_code_visiting' => '71200-000',
            ],
        ];

        foreach ($contacts as $contactData) {
            $contact = Contact::create([
                'id' => Str::uuid()->toString(),
                'type' => $contactData['type'],
                'responsible_user_id' => $responsibleUser->id,
                'client_id' => $client->id,
                'name' => $contactData['name'],
                'email' => $contactData['email'],
                'phone' => $contactData['phone'] ?? null,
                'website' => $contactData['website'] ?? null,
                'street_visiting' => $contactData['street_visiting'] ?? null,
                'house_number_visiting' => $contactData['house_number_visiting'] ?? null,
                'postal_code_visiting' => $contactData['postal_code_visiting'] ?? null,
                'city_visiting' => $contactData['city_visiting'] ?? null,
                'state_visiting' => $contactData['state_visiting'] ?? null,
                'country_visiting' => $contactData['country_visiting'] ?? null,
            ]);

            if ($contactData['type'] !== 'location') {
                $person = ContactPerson::create([
                    'id' => Str::uuid()->toString(),
                    'contact_id' => $contact->id,
                    'first_name' => 'João',
                    'last_name' => 'Silva',
                    'mobile' => '+55 11 98765-4321',
                    'role' => 'Gerente Comercial',
                ]);

                ContactPersonEmail::create([
                    'id' => Str::uuid()->toString(),
                    'contact_person_id' => $person->id,
                    'email' => 'joao.silva@' . strtolower(str_replace(' ', '', explode(' ', $contactData['name'])[0])) . '.com',
                ]);

                ContactPersonNote::create([
                    'id' => Str::uuid()->toString(),
                    'contact_person_id' => $person->id,
                    'name' => 'Primeiro Contato',
                    'content' => 'Cliente demonstrou interesse em nossos produtos e serviços.',
                    'note_date' => now(),
                    'created_by' => $responsibleUser->id,
                ]);
            }

            $this->command->info("Contato criado: {$contact->name}");
        }

        $this->command->info('Seeders de contatos criados com sucesso!');
    }
}
