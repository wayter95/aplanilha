# Estrutura do Banco de Dados - Sistema Multi-Tenant

## Visão Geral

Este documento descreve a estrutura completa do banco de dados PostgreSQL para o sistema multi-tenant com autenticação e controle de acesso baseado em roles e permissions.

## Características Principais

-   **UUID v7** para todas as primary keys
-   **Multi-tenancy** com isolamento lógico por `client_id`
-   **Soft deletes** nas tabelas principais
-   **Constraints de unicidade** respeitando o contexto multi-tenant
-   **Indexes otimizados** para performance
-   **Auditoria completa** com logs de atividade

## Tabelas

### 1. client_subscribes

**Propósito**: Registra cada tenant (cliente) do sistema

| Campo      | Tipo         | Regras       |
| ---------- | ------------ | ------------ |
| id         | uuid         | PK           |
| name       | varchar(255) | not null     |
| subdomain  | varchar(255) | unique       |
| email      | varchar(255) | unique       |
| cnpj       | varchar(255) | unique       |
| plan       | varchar(100) | nullable     |
| active     | boolean      | default true |
| expires_at | timestamp    | nullable     |
| created_at | timestamp    |              |
| updated_at | timestamp    |              |

**Indexes**:

-   `subdomain` (unique)
-   `email` (unique)
-   `cnpj` (unique)
-   `[subdomain, active]`
-   `[email, active]`
-   `[cnpj, active]`

### 2. users

**Propósito**: Usuários globais e por tenant

| Campo             | Tipo         | Regras                                                      |
| ----------------- | ------------ | ----------------------------------------------------------- |
| id                | uuid         | PK                                                          |
| client_id         | uuid         | FK → client_subscribes.id, nullable (null = usuário Master) |
| name              | varchar(255) | not null                                                    |
| email             | varchar(255) |                                                             |
| password          | varchar(255) | not null                                                    |
| avatar_path       | varchar(255) | nullable                                                    |
| is_master         | boolean      | default false                                               |
| email_verified_at | timestamp    | nullable                                                    |
| remember_token    | varchar(100) | nullable                                                    |
| created_at        | timestamp    |                                                             |
| updated_at        | timestamp    |                                                             |
| deleted_at        | timestamp    | nullable (soft delete)                                      |

**Constraints**:

-   `unique([client_id, email])` - Email único por tenant
-   `foreign(client_id) → client_subscribes(id)`

**Indexes**:

-   `[client_id, email]`
-   `[is_master]`
-   `[email]`

### 3. user_roles

**Propósito**: Perfis de acesso por tenant

| Campo       | Tipo         | Regras                    |
| ----------- | ------------ | ------------------------- |
| id          | uuid         | PK                        |
| client_id   | uuid         | FK → client_subscribes.id |
| name        | varchar(100) | not null                  |
| description | varchar(255) | nullable                  |
| created_at  | timestamp    |                           |
| updated_at  | timestamp    |                           |

**Constraints**:

-   `unique([client_id, name])` - Nome único por tenant
-   `foreign(client_id) → client_subscribes(id)`

**Indexes**:

-   `[client_id, name]`

### 4. user_permissions

**Propósito**: Permissões granulares por tenant

| Campo      | Tipo         | Regras                                 |
| ---------- | ------------ | -------------------------------------- |
| id         | uuid         | PK                                     |
| client_id  | uuid         | FK → client_subscribes.id              |
| module     | varchar(100) | ex: 'users', 'payments', 'reports'     |
| action     | varchar(100) | ex: 'create', 'edit', 'view', 'delete' |
| created_at | timestamp    |                                        |
| updated_at | timestamp    |                                        |

**Constraints**:

-   `unique([client_id, module, action])` - Permissão única por tenant
-   `foreign(client_id) → client_subscribes(id)`

**Indexes**:

-   `[client_id, module]`
-   `[client_id, action]`

### 5. role_user (Pivot)

**Propósito**: Associação entre usuários e roles

| Campo      | Tipo      | Regras             |
| ---------- | --------- | ------------------ |
| user_id    | uuid      | FK → users.id      |
| role_id    | uuid      | FK → user_roles.id |
| created_at | timestamp |                    |
| updated_at | timestamp |                    |

**Constraints**:

-   `primary([user_id, role_id])`
-   `foreign(user_id) → users(id)`
-   `foreign(role_id) → user_roles(id)`

**Indexes**:

-   `[user_id]`
-   `[role_id]`

### 6. permission_role (Pivot)

**Propósito**: Associação entre roles e permissões

| Campo         | Tipo      | Regras                   |
| ------------- | --------- | ------------------------ |
| role_id       | uuid      | FK → user_roles.id       |
| permission_id | uuid      | FK → user_permissions.id |
| created_at    | timestamp |                          |
| updated_at    | timestamp |                          |

**Constraints**:

-   `primary([role_id, permission_id])`
-   `foreign(role_id) → user_roles(id)`
-   `foreign(permission_id) → user_permissions(id)`

**Indexes**:

-   `[role_id]`
-   `[permission_id]`

### 7. password_reset_tokens

**Propósito**: Tokens para recuperação de senha

| Campo      | Tipo         | Regras   |
| ---------- | ------------ | -------- |
| email      | varchar(255) | PK       |
| token      | varchar(255) |          |
| created_at | timestamp    | nullable |

### 8. sessions

**Propósito**: Sessões de usuário

| Campo         | Tipo         | Regras                  |
| ------------- | ------------ | ----------------------- |
| id            | varchar(255) | PK                      |
| user_id       | uuid         | FK → users.id, nullable |
| ip_address    | varchar(45)  | nullable                |
| user_agent    | text         | nullable                |
| payload       | longtext     |                         |
| last_activity | integer      |                         |

**Constraints**:

-   `foreign(user_id) → users(id)`

**Indexes**:

-   `[user_id]`
-   `[last_activity]`

### 9. activity_logs

**Propósito**: Logs de auditoria

| Campo      | Tipo         | Regras                                       |
| ---------- | ------------ | -------------------------------------------- |
| id         | uuid         | PK                                           |
| client_id  | uuid         | FK → client_subscribes.id, nullable          |
| user_id    | uuid         | FK → users.id, nullable                      |
| action     | varchar(255) | ex: 'login', 'create_user', 'update_profile' |
| model_type | varchar(255) | nullable, ex: 'User', 'ClientSubscribe'      |
| model_id   | uuid         | nullable, ID do modelo afetado               |
| old_values | json         | nullable, valores anteriores                 |
| new_values | json         | nullable, novos valores                      |
| ip_address | varchar(45)  | nullable                                     |
| user_agent | text         | nullable                                     |
| created_at | timestamp    |                                              |
| updated_at | timestamp    |                                              |

**Constraints**:

-   `foreign(client_id) → client_subscribes(id)`
-   `foreign(user_id) → users(id)`

**Indexes**:

-   `[client_id, created_at]`
-   `[user_id, created_at]`
-   `[action, created_at]`
-   `[model_type, model_id]`

## Relacionamentos

```
client_subscribes (1) ←→ (N) users
client_subscribes (1) ←→ (N) user_roles
client_subscribes (1) ←→ (N) user_permissions
client_subscribes (1) ←→ (N) activity_logs

users (N) ←→ (N) user_roles [via role_user]
user_roles (N) ←→ (N) user_permissions [via permission_role]

users (1) ←→ (N) sessions
users (1) ←→ (N) activity_logs
```

## Perfis Padrão

### Master

-   `client_id`: null
-   `is_master`: true
-   Acesso total ao sistema
-   Gerenciamento de clientes

### Admin

-   `client_id`: específico do tenant
-   Controle total dentro do tenant
-   Gerenciamento de usuários do tenant

### User

-   `client_id`: específico do tenant
-   Acesso restrito conforme permissões
-   Não pode conceder permissões que não possui

## Mecanismos de Multi-Tenant

1. **Resolução de Tenant**: Middleware resolve subdomínio → client_id
2. **Global Scope**: Filtra automaticamente por client_id
3. **Policies**: Valida ações no contexto do tenant
4. **Fallback**: Cliente demo.aplanilha.com como padrão

## Migrations Criadas

1. `2025_10_17_143621_create_client_subscribes_table.php`
2. `0001_01_01_000000_create_users_table.php` (modificada)
3. `2025_10_17_143654_create_user_roles_table.php`
4. `2025_10_17_143655_create_user_permissions_table.php`
5. `2025_10_17_143716_create_role_user_table.php`
6. `2025_10_17_143717_create_permission_role_table.php`
7. `2025_10_17_143733_create_activity_logs_table.php`

## Próximos Passos

1. Executar migrations: `php artisan migrate`
2. Criar seeders para dados iniciais
3. Implementar models com relacionamentos
4. Criar middleware de tenant resolution
5. Implementar global scopes
6. Criar policies de autorização
