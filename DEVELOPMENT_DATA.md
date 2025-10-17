# Dados de Desenvolvimento

## Clientes Criados

### Cliente Demo (Fallback)

-   **Subdomínio**: `demo`
-   **Nome**: Cliente Demo
-   **Email**: demo@aplanilha.com
-   **CNPJ**: 12.345.678/0001-90
-   **Plano**: demo
-   **Status**: Ativo
-   **Expiração**: 1 ano

### Cliente Teste

-   **Subdomínio**: `teste`
-   **Nome**: Empresa Teste
-   **Email**: teste@aplanilha.com
-   **CNPJ**: 98.765.432/0001-10
-   **Plano**: premium
-   **Status**: Ativo
-   **Expiração**: 6 meses

## Usuários Criados

### Usuário Master (Global)

-   **Email**: master@aplanilha.com
-   **Senha**: password
-   **Tipo**: Master (is_master = true)
-   **Client ID**: null (global)

### Usuários por Cliente

#### Cliente Demo

-   **Admin**: admin@demo.com / password
-   **User**: demo@demo.com / password

#### Cliente Teste

-   **Admin**: admin@teste.com / password
-   **User**: demo@teste.com / password

## Roles Criados

Para cada cliente:

-   **Admin**: Administrador completo do tenant
-   **User**: Usuário padrão com permissões limitadas

## Permissões Criadas

Para cada cliente:

-   **users**: view, create, edit, delete
-   **profile**: view, edit
-   **dashboard**: view
-   **reports**: view, create
-   **settings**: view, edit

## Associações de Permissões

### Role Admin

-   Todas as permissões disponíveis

### Role User

-   Apenas: profile, dashboard, reports

## URLs de Acesso

### Desenvolvimento Local

-   **Sem subdomínio**: http://localhost (usa cliente demo)
-   **Cliente Demo**: http://demo.localhost
-   **Cliente Teste**: http://teste.localhost

### Produção (exemplo)

-   **Cliente Demo**: https://demo.aplanilha.com
-   **Cliente Teste**: https://teste.aplanilha.com

## Credenciais de Teste

### Master User

-   **Email**: master@aplanilha.com
-   **Senha**: password

### Cliente Demo

-   **Admin**: admin@demo.com / password
-   **User**: demo@demo.com / password

### Cliente Teste

-   **Admin**: admin@teste.com / password
-   **User**: demo@teste.com / password

## Comandos Úteis

### Recriar dados de desenvolvimento

```bash
docker-compose exec app php artisan db:seed
```

### Verificar dados

```bash
docker-compose exec app php artisan tinker
```

### Limpar e recriar tudo

```bash
docker-compose exec app php artisan migrate:fresh --seed
```
