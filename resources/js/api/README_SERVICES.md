# 📚 Guia de Services Layer - API

## 🎯 Visão Geral

A camada de **Services** centraliza toda a lógica de comunicação com a API, removendo código de fetch/axios dos componentes Vue. Isso torna o código mais limpo, reutilizável e fácil de manter.

---

## 🏗️ Arquitetura

```
resources/js/api/
├── client.js              # Cliente Axios configurado
├── baseService.js         # Classe base com CRUD genérico
├── projectTypesService.js # Service específico
├── documentTypesService.js
├── documentTemplates.js
└── users.js
```

---

## 📦 BaseService - Classe Genérica

Todos os services herdam do `BaseService`, que fornece métodos CRUD padrão:

```javascript
import BaseService from './baseService'

class MyService extends BaseService {
  constructor() {
    super('/my-endpoint') // Define o endpoint base
  }
}
```

### Métodos Disponíveis no BaseService:

| Método | Descrição | Exemplo |
|--------|-----------|---------|
| `list(params)` | Lista recursos com filtros | `list({ search: 'teste', page: 1 })` |
| `get(id)` | Busca recurso por ID | `get(1)` |
| `create(data)` | Cria novo recurso | `create({ name: 'Novo' })` |
| `update(id, data)` | Atualiza recurso | `update(1, { name: 'Atualizado' })` |
| `delete(id)` | Deleta recurso | `delete(1)` |
| `request(method, path, data, config)` | Requisição customizada | `request('PATCH', '/1/activate')` |

---

## 🔧 Como Criar um Novo Service

### Passo 1: Criar o arquivo do service

```javascript
// resources/js/api/myService.js
import BaseService from './baseService'

class MyService extends BaseService {
    constructor() {
        super('/my-resource') // Endpoint base: /api/my-resource
    }

    // Métodos específicos do seu recurso
    async activate(id) {
        return this.request('PATCH', `/${id}/activate`)
    }

    async deactivate(id) {
        return this.request('PATCH', `/${id}/deactivate`)
    }
}

// Exportar instância única (singleton)
export default new MyService()
```

### Passo 2: Usar no componente

```vue
<script setup>
import myService from '@/api/myService'
import { useToast } from '@/composables/useToast'

const toast = useToast()

const loadData = async () => {
  try {
    const data = await myService.list({ search: 'teste' })
    console.log(data)
  } catch (error) {
    toast.error('Erro ao carregar dados')
  }
}

const saveData = async (formData) => {
  try {
    const result = await myService.create(formData)
    if (result.success) {
      toast.success(result.message)
    }
  } catch (error) {
    toast.error('Erro ao salvar')
  }
}
</script>
```

---

## 📝 Exemplo Completo: ProjectTypesService

```javascript
import BaseService from './baseService'

class ProjectTypesService extends BaseService {
    constructor() {
        super('/project-types')
    }

    // Ativa um tipo de projeto
    async activate(id) {
        return this.request('PATCH', `/${id}/activate`)
    }

    // Bloqueia um tipo de projeto
    async block(id) {
        return this.request('PATCH', `/${id}/block`)
    }

    // Alterna status (ativo ↔ bloqueado)
    async toggleStatus(id, currentStatus) {
        return currentStatus === 'a' 
            ? this.block(id) 
            : this.activate(id)
    }

    // Lista apenas ativos
    async listActive() {
        return this.list({ status: 'Ativo' })
    }

    // Lista apenas bloqueados
    async listBlocked() {
        return this.list({ status: 'Bloqueado' })
    }
}

export default new ProjectTypesService()
```

---

## 🎨 Uso nos Componentes

### **Antes** (com fetch direto):

```javascript
async save() {
  try {
    const url = this.mode === 'edit' 
      ? `/api/project-types/${this.id}`
      : '/api/project-types'
    
    const method = this.mode === 'edit' ? 'PUT' : 'POST'
    
    const response = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify(this.form)
    })

    const data = await response.json()
    
    if (data.success) {
      toast.success(data.message)
    }
  } catch (error) {
    toast.error('Erro ao salvar')
  }
}
```

### **Depois** (com service):

```javascript
import projectTypesService from '@/api/projectTypesService'

async save() {
  try {
    const data = this.mode === 'edit'
      ? await projectTypesService.update(this.id, this.form)
      : await projectTypesService.create(this.form)
    
    if (data.success) {
      toast.success(data.message)
    }
  } catch (error) {
    toast.error('Erro ao salvar')
  }
}
```

**Redução de código: 60%!** 🎉

---

## ✅ Benefícios

### 1. **Código mais Limpo**
- Componentes focam apenas na lógica de UI
- API isolada em um único lugar

### 2. **Reutilização**
- Mesmos métodos usados em múltiplos componentes
- Evita duplicação de código

### 3. **Fácil Manutenção**
- Alterar endpoint? Muda em 1 lugar
- Adicionar autenticação? Faz no client.js

### 4. **Testabilidade**
- Services podem ser testados isoladamente
- Fácil criar mocks

### 5. **Tipagem (TypeScript)**
- Fácil adicionar tipos para autocomplete
- Menos erros em tempo de desenvolvimento

---

## 🔄 Migração de Componentes Existentes

### Checklist para migrar um componente:

1. ✅ Criar service na pasta `api/`
2. ✅ Importar service no componente
3. ✅ Substituir `fetch()` por `service.method()`
4. ✅ Remover headers CSRF manuais
5. ✅ Simplificar tratamento de erros
6. ✅ Testar funcionalidade

---

## 🚀 Próximos Passos

### Services a criar:

- [ ] `documentTypesService.js` (já existe, precisa refatorar)
- [ ] `documentTemplatesService.js` (já existe, precisa refatorar)
- [ ] `usersService.js` (já existe, precisa refatorar)
- [ ] `rolesService.js`
- [ ] `projectsService.js`
- [ ] `reportsService.js`

### Componentes a migrar:

- [ ] `DocumentTypes/Form.vue`
- [ ] `DocumentTypes/Index.vue`
- [ ] `DocumentTemplates/Form.vue`
- [ ] `DocumentTemplates/Index.vue`
- [ ] `Users.vue`
- [ ] `Roles.vue`

---

## 📚 Recursos Adicionais

### Client Axios Configurado

O `client.js` já está configurado com:
- ✅ Interceptors de request/response
- ✅ CSRF token automático
- ✅ Loading states globais
- ✅ Tratamento de erros 401/403/500
- ✅ Timeout de 30 segundos

### Documentação Oficial

- [Axios Documentation](https://axios-http.com/docs/intro)
- [Vue Composables](https://vuejs.org/guide/reusability/composables.html)

---

## 💡 Dicas

1. **Sempre use try/catch** nos componentes
2. **Retorne a resposta completa** do service
3. **Deixe o componente decidir** como tratar o erro
4. **Use singleton** para services (export default new Service())
5. **Documente métodos específicos** com JSDoc

---

**Resultado:** Código mais limpo, organizado e profissional! 🎯
