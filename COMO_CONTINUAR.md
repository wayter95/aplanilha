# 🎯 Como Continuar a Refatoração

**Objetivo:** Chegar de 8/10 para 9/10

---

## 📋 CHECKLIST: Componentes para Refatorar

### Prioridade ALTA (window.axios):
- [ ] `Pages/DocumentTemplateEditor.vue` (~6 window.axios)
- [ ] `Pages/DocumentTypes/Form.vue` (~3 window.axios)
- [ ] `Components/DocumentTypesModal.vue` (~4 window.axios)
- [ ] `Pages/Users/Form.vue` (se existir)
- [ ] `Pages/Roles/Form.vue` (se existir)

### Buscar mais componentes:
```bash
# Listar TODOS os componentes com window.axios
grep -r "window\.axios" resources/js/Pages --include="*.vue" -l

# Ver quantas ocorrências em cada arquivo
grep -r "window\.axios" resources/js/Pages --include="*.vue" -c
```

---

## 🔧 PASSO A PASSO: Como Refatorar Componente

### Exemplo: Refatorar UserForm.vue

#### 1. Identificar window.axios
```bash
grep "window\.axios" resources/js/Pages/Users/Form.vue
```

**Resultado exemplo:**
```javascript
await window.axios.get('/api/users')
await window.axios.post('/api/users', data)
await window.axios.put(`/api/users/${id}`, data)
await window.axios.delete(`/api/users/${id}`)
```

---

#### 2. Verificar se API service existe
**Já existe:** `api/users.js` ✅

Se NÃO existir, criar:
```javascript
// api/users.js
import { get, post, put, del } from './client'

export const usersApi = {
  getAll: (params) => get('/users', { params }),
  getById: (id) => get(`/users/${id}`),
  create: (data) => post('/users', data),
  update: (id, data) => put(`/users/${id}`, data),
  delete: (id) => del(`/users/${id}`)
}

export default usersApi
```

---

#### 3. Criar composable (se não existir)
```javascript
// composables/useUsers.js
import { ref } from 'vue'
import { usersApi } from '@/api'

export function useUsers() {
  const loading = ref(false)
  const error = ref(null)
  const users = ref([])

  const fetchAll = async (params = {}) => {
    loading.value = true
    error.value = null
    try {
      const { data } = await usersApi.getAll(params)
      users.value = data
      return data
    } catch (e) {
      error.value = e
      throw e
    } finally {
      loading.value = false
    }
  }

  const create = async (userData) => {
    loading.value = true
    error.value = null
    try {
      const { data } = await usersApi.create(userData)
      await fetchAll() // Atualizar lista
      return data
    } catch (e) {
      error.value = e
      throw e
    } finally {
      loading.value = false
    }
  }

  const update = async (id, userData) => {
    loading.value = true
    error.value = null
    try {
      const { data } = await usersApi.update(id, userData)
      await fetchAll() // Atualizar lista
      return data
    } catch (e) {
      error.value = e
      throw e
    } finally {
      loading.value = false
    }
  }

  const remove = async (id) => {
    loading.value = true
    error.value = null
    try {
      await usersApi.delete(id)
      await fetchAll() // Atualizar lista
      return true
    } catch (e) {
      error.value = e
      throw e
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    error,
    users,
    fetchAll,
    create,
    update,
    remove
  }
}
```

---

#### 4. Refatorar componente

**ANTES:**
```vue
<script>
export default {
  data() {
    return {
      users: [],
      loading: false
    }
  },
  methods: {
    async fetchUsers() {
      this.loading = true
      try {
        const { data } = await window.axios.get('/api/users')
        this.users = data
      } catch (error) {
        console.error(error)
      } finally {
        this.loading = false
      }
    },
    async saveUser(userData) {
      await window.axios.post('/api/users', userData)
      await this.fetchUsers()
    }
  },
  created() {
    this.fetchUsers()
  }
}
</script>
```

**DEPOIS:**
```vue
<script>
import { useUsers } from '@/composables/useUsers'
import { useToast } from '@/composables/useToast'

export default {
  setup() {
    const {
      loading,
      users,
      fetchAll,
      create,
      update,
      remove
    } = useUsers()

    const toast = useToast()

    return {
      loading,
      users,
      fetchAll,
      apiCreate: create,
      apiUpdate: update,
      apiRemove: remove,
      toast
    }
  },
  data() {
    return {
      // Dados locais do componente (se necessário)
    }
  },
  methods: {
    async saveUser(userData) {
      try {
        await this.apiCreate(userData)
        this.toast.success('Usuário criado com sucesso!')
      } catch (e) {
        this.toast.error('Erro ao criar usuário')
        console.error(e)
      }
    }
  },
  created() {
    this.fetchAll()
  }
}
</script>
```

---

#### 5. Testar
```bash
# Compilar
npm run dev

# Verificar no navegador:
# 1. Lista carrega?
# 2. Criar funciona?
# 3. Editar funciona?
# 4. Deletar funciona?
# 5. Mensagens de erro aparecem?
```

---

## 🎯 TEMPLATE RÁPIDO

### Para criar composable novo:
```javascript
// composables/use[NomeDoRecurso].js
import { ref } from 'vue'
import { [recurso]Api } from '@/api'

export function use[NomeDoRecurso]() {
  const loading = ref(false)
  const error = ref(null)
  const items = ref([])

  const fetchAll = async (params = {}) => {
    loading.value = true
    error.value = null
    try {
      const { data } = await [recurso]Api.getAll(params)
      items.value = data
      return data
    } catch (e) {
      error.value = e
      throw e
    } finally {
      loading.value = false
    }
  }

  // ... outros métodos (create, update, remove)

  return {
    loading,
    error,
    items,
    fetchAll
    // ... outros
  }
}
```

---

## 📊 TRACKING DE PROGRESSO

### Manter atualizado:
```bash
# Contar window.axios restantes
grep -r "window\.axios" resources/js/Pages --include="*.vue" | wc -l

# Ver por componente
grep -r "window\.axios" resources/js/Pages --include="*.vue" -c | grep -v ":0"
```

### Meta:
- **Inicial:** ~30 ocorrências
- **Atual:** ~24 ocorrências (após DocumentTemplates)
- **Meta:** 0 ocorrências

---

## ⚠️ CUIDADOS

### 1. NÃO quebrar funcionalidade
- Sempre testar após refatorar
- Fazer commit antes de começar
- Refatorar 1 componente por vez

### 2. Manter CSS igual
- **NÃO modificar classes CSS**
- **NÃO modificar templates HTML**
- **APENAS refatorar JavaScript**

### 3. Tratamento de erro
- Sempre usar try/catch
- Sempre mostrar toast de erro
- Sempre logar erro no console

---

## 🎯 METAS

### Semana 1:
- [ ] Refatorar 5 componentes com window.axios
- [ ] Criar 2 composables novos
- [ ] Documentar padrões

### Semana 2:
- [ ] Refatorar 10 componentes restantes
- [ ] Adicionar TypeScript em 3 composables
- [ ] Setup básico de testes

### Meta Final:
- [ ] 0 window.axios nos componentes
- [ ] 100% composables para lógica de API
- [ ] Documentação completa
- [ ] Nota: 9/10 ✅

---

## 📚 REFERÊNCIAS

### Arquivos de exemplo:
- `Pages/DocumentTemplates.vue` - Componente refatorado
- `composables/useDocumentTemplates.js` - Composable completo
- `api/documentTemplates.js` - API service

### Documentação:
- `ANALISE_ESTRUTURA_JS.md` - Críticas e padrões
- `PROGRESSO_REFATORACAO.md` - Tracking detalhado
- `REFATORACAO_RESUMO.md` - Resumo completo

---

**Boa sorte! 🚀**

Qualquer dúvida, usar DocumentTemplates.vue como referência.
