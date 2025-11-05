# 📘 Guia Completo: Sistema de Tabs em Novos Módulos

**Versão:** 1.0  
**Última atualização:** 2025-11-05

---

## 📋 Índice

1. [Visão Geral](#visão-geral)
2. [Passo a Passo Completo](#passo-a-passo-completo)
3. [Exemplos Práticos](#exemplos-práticos)
4. [API de Referência](#api-de-referência)
5. [Casos de Uso Avançados](#casos-de-uso-avançados)
6. [Troubleshooting](#troubleshooting)
7. [Boas Práticas](#boas-práticas)

---

## 🎯 Visão Geral

O sistema de tabs permite criar formulários que abrem em abas (tabs) no topo da aplicação, mantendo o estado mesmo quando o usuário navega entre páginas. É ideal para:

- ✅ Formulários de criação/edição
- ✅ Workflows multi-etapas
- ✅ Múltiplos formulários abertos simultaneamente

---

## 🚀 Passo a Passo Completo

### Passo 1: Criar o Componente de Formulário

Crie seu componente Vue em `resources/js/Pages/[SeuModulo]/Form.vue`:

```vue
<template>
  <AppLayout 
    v-if="standalone" 
    :title="computedTitle" 
    :description="''" 
    :user="user"
  >
    <Form @submit="handleSave" :initial-values="form" :key="formKey">
      <!-- Seus campos aqui -->
      <Input name="name" label="Nome" v-model="form.name" />
      <Button type="submit">Salvar</Button>
    </Form>
  </AppLayout>
  
  <!-- Conteúdo quando não standalone (dentro de tab) -->
  <div v-else class="p-6">
    <Form @submit="handleSave" :initial-values="form" :key="formKey">
      <!-- Seus campos aqui -->
      <Input name="name" label="Nome" v-model="form.name" />
      <Button type="submit">Salvar</Button>
    </Form>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { useTabForm } from '@/composables/useTabForm'
import AppLayout from '@/Layouts/AppLayout.vue'
import Form from '@/Components/Form/Form.vue'
import Input from '@/Components/Form/Input.vue'
import Button from '@/Components/Button.vue'
import { useToast } from '@/composables/useToast'

const props = defineProps({
  mode: { type: String, default: 'create' },
  id: String,
  tempKey: String,
  standalone: { type: Boolean, default: false },
})

const page = usePage()
const user = computed(() => page.props.user)
const toast = useToast()

// Seu modelo de dados
const form = ref({
  name: '',
  email: '',
  // ... outros campos
})

const formKey = ref(0)

// ============================================================================
// CONFIGURAÇÃO DO SISTEMA DE TABS
// ============================================================================

const {
  tabKey,
  isInitializing,
  updateTabTitle,
  saveFormData,
  loadFormData,
  convertToEdit,
} = useTabForm({
  componentName: 'UsersForm', // ⚠️ IMPORTANTE: Nome do componente
  context: 'users', // ⚠️ IMPORTANTE: Contexto da rota (sem /)
  mode: props.mode,
  id: props.id,
  tempKey: props.tempKey,
  
  // Função para gerar título dinâmico
  getTitle: (form, mode) => {
    if (mode === 'edit') {
      return form.name || 'Usuário'
    }
    return 'Novo Usuário'
  },
  
  // Títulos padrão
  defaultCreateTitle: 'Novo Usuário',
  defaultEditTitle: 'Carregando…',
  
  // Callbacks opcionais
  onTabCreated: (tab) => {
    console.log('Tab criada:', tab)
  },
  
  beforeConvertToEdit: async (tempKey, newId) => {
    // Validações antes de converter create → edit
    return true
  },
  
  afterConvertToEdit: (tempKey, newId, tab) => {
    console.log('Convertido para edit:', tab)
  },
})

// Título computado
const computedTitle = computed(() => {
  return props.mode === 'edit' 
    ? (form.value.name || 'Usuário') 
    : 'Novo Usuário'
})

// ============================================================================
// CARREGAMENTO DE DADOS
// ============================================================================

onMounted(async () => {
  // 1. Carrega dados salvos do localStorage (se houver)
  const savedData = loadFormData()
  if (savedData) {
    Object.assign(form.value, savedData)
    formKey.value++ // Força re-render do formulário
  }
  
  // 2. Se estiver em modo edit, carrega do servidor
  if (props.mode === 'edit' && props.id) {
    await loadFromServer()
  }
  
  // 3. Atualiza título da tab
  updateTabTitle(form.value)
})

async function loadFromServer() {
  try {
    const { data } = await window.axios.get(`/api/users/${props.id}`)
    Object.assign(form.value, data)
    formKey.value++
    updateTabTitle(form.value)
  } catch (error) {
    console.error('Erro ao carregar:', error)
    toast.error('Erro ao carregar dados')
  }
}

// ============================================================================
// SALVAMENTO DE DADOS
// ============================================================================

// Salva automaticamente no localStorage ao mudar campos
watch(form, (newForm) => {
  saveFormData(newForm)
  updateTabTitle(newForm)
}, { deep: true })

// ============================================================================
// AÇÕES DO FORMULÁRIO
// ============================================================================

async function handleSave(values) {
  try {
    if (props.mode === 'create') {
      // Criar novo registro
      const { data } = await window.axios.post('/api/users', values)
      
      toast.success('Usuário criado com sucesso!')
      
      // Converte tab de create → edit
      await convertToEdit(data.id, values.name)
      
      // Navega para a página de edição
      router.visit(`/users/${data.id}/edit`)
    } else {
      // Atualizar registro existente
      await window.axios.put(`/api/users/${props.id}`, values)
      
      toast.success('Usuário atualizado com sucesso!')
      updateTabTitle(form.value)
    }
  } catch (error) {
    console.error('Erro ao salvar:', error)
    toast.error(error.response?.data?.message || 'Erro ao salvar')
  }
}
</script>
```

---

### Passo 2: Registrar o Componente

No arquivo `resources/js/config/tabComponents.ts`, adicione:

```typescript
// ... imports e código existente ...

// Registrar seu componente
registerTabComponent('UsersForm', () => import('@/Pages/Users/Form.vue'))
```

**⚠️ IMPORTANTE:** O nome usado aqui (`'UsersForm'`) deve ser **exatamente** o mesmo usado em `componentName` no `useTabForm`.

---

### Passo 3: Criar Rotas (Backend)

No arquivo `routes/web.php`:

```php
use App\Http\Controllers\UserController;

Route::middleware(['web', 'auth'])->group(function () {
    // Listagem
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    
    // Criar novo (gera tempKey)
    Route::get('/users/new/{tempKey?}', [UserController::class, 'create'])
        ->name('users.create');
    
    // Editar existente
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])
        ->name('users.edit');
});
```

---

### Passo 4: Criar Controller (Backend)

No arquivo `app/Http/Controllers/UserController.php`:

```php
<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Sua lógica de listagem
        return Inertia::render('Users/Index', [
            'users' => [], // Seus dados
        ]);
    }
    
    public function create(Request $request, $tempKey = null)
    {
        // Gera tempKey se não fornecido
        if (!$tempKey) {
            $tempKey = 'temp-' . time() . '-' . rand(1000, 9999);
            return redirect()->route('users.create', ['tempKey' => $tempKey]);
        }
        
        return Inertia::render('Users/Form', [
            'mode' => 'create',
            'tempKey' => $tempKey,
            'standalone' => false,
        ]);
    }
    
    public function edit($id)
    {
        return Inertia::render('Users/Form', [
            'mode' => 'edit',
            'id' => $id,
            'standalone' => false,
        ]);
    }
}
```

---

### Passo 5: Criar Página de Listagem (Opcional)

Na página de listagem (`resources/js/Pages/Users/Index.vue`), adicione botões para abrir formulários:

```vue
<template>
  <AppLayout title="Usuários" :user="user">
    <div class="p-6">
      <button @click="openCreateTab" class="btn btn-primary">
        Novo Usuário
      </button>
      
      <table>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.name }}</td>
          <td>
            <button @click="openEditTab(user)">
              Editar
            </button>
          </td>
        </tr>
      </table>
    </div>
  </AppLayout>
</template>

<script setup>
import { useTabsStore } from '@/stores/useTabsStore'
import { createTabConfig, generateTempKey } from '@/utils/tabHelpers'
import { router } from '@inertiajs/vue3'

const tabsStore = useTabsStore()

function openCreateTab() {
  const tempKey = generateTempKey()
  const tab = createTabConfig({
    componentName: 'UsersForm',
    context: 'users',
    mode: 'create',
    tempKey,
    title: 'Novo Usuário',
  })
  
  tabsStore.addTab(tab)
  router.visit(`/users/new/${tempKey}`)
}

function openEditTab(user) {
  // Verifica se tab já existe
  const existingTab = tabsStore.tabs.find(t => t.key === user.id)
  if (existingTab) {
    tabsStore.setActive(existingTab)
    router.visit(existingTab.path)
    return
  }
  
  // Cria nova tab
  const tab = createTabConfig({
    componentName: 'UsersForm',
    context: 'users',
    mode: 'edit',
    id: user.id,
    title: user.name || 'Usuário',
  })
  
  tabsStore.addTab(tab)
  router.visit(`/users/${user.id}/edit`)
}
</script>
```

---

## 📝 Exemplos Práticos

### Exemplo 1: Formulário Simples

```vue
<script setup>
import { useTabForm } from '@/composables/useTabForm'

const form = ref({ name: '' })

const { tabKey, updateTabTitle, saveFormData } = useTabForm({
  componentName: 'ProductsForm',
  context: 'products',
  mode: 'create',
  getTitle: (form) => form.name || 'Novo Produto',
})

watch(form, (newForm) => {
  saveFormData(newForm)
  updateTabTitle(newForm)
}, { deep: true })
</script>
```

### Exemplo 2: Formulário com Validação Antes de Fechar

```vue
<script setup>
import { useTabsStore } from '@/stores/useTabsStore'
import { useTabForm } from '@/composables/useTabForm'

const tabsStore = useTabsStore()
const form = ref({ name: '', email: '' })
const hasUnsavedChanges = ref(false)

// Registra hooks
tabsStore.registerHooks('UsersForm', {
  beforeClose: async (tab) => {
    if (hasUnsavedChanges.value) {
      return confirm('Tem alterações não salvas. Deseja fechar mesmo assim?')
    }
    return true
  },
})

const { saveFormData } = useTabForm({
  componentName: 'UsersForm',
  context: 'users',
  mode: 'create',
  getTitle: (form) => form.name || 'Novo Usuário',
})

watch(form, () => {
  hasUnsavedChanges.value = true
  saveFormData(form.value)
}, { deep: true })
</script>
```

### Exemplo 3: Formulário com Múltiplas Etapas

```vue
<script setup>
const currentStep = ref(1)
const form = ref({
  step1: { name: '' },
  step2: { email: '' },
  step3: { phone: '' },
})

const { updateTabTitle } = useTabForm({
  componentName: 'MultiStepForm',
  context: 'wizard',
  mode: 'create',
  getTitle: (form, mode) => {
    return `Novo Registro - Etapa ${currentStep.value}`
  },
})

watch(currentStep, () => {
  updateTabTitle(form.value)
})
</script>
```

---

## 📚 API de Referência

### `useTabForm(config)`

#### Parâmetros

| Parâmetro | Tipo | Obrigatório | Descrição |
|-----------|------|-------------|-----------|
| `componentName` | `string` | ✅ | Nome do componente (deve estar registrado) |
| `context` | `string` | ✅ | Contexto da rota (ex: 'users', 'products') |
| `mode` | `'create' \| 'edit'` | ✅ | Modo da tab |
| `id` | `string` | ⚠️ | ID do registro (obrigatório para edit) |
| `tempKey` | `string` | ⚠️ | Chave temporária (obrigatório para create) |
| `getTitle` | `(form, mode) => string` | ✅ | Função para gerar título |
| `defaultCreateTitle` | `string` | ❌ | Título padrão para criação |
| `defaultEditTitle` | `string` | ❌ | Título padrão para edição |
| `props` | `Record<string, any>` | ❌ | Props adicionais |
| `onTabCreated` | `(tab) => void` | ❌ | Callback quando tab é criada |
| `onTabUpdated` | `(tab) => void` | ❌ | Callback quando tab é atualizada |
| `beforeConvertToEdit` | `(tempKey, newId) => boolean` | ❌ | Validação antes de converter |
| `afterConvertToEdit` | `(tempKey, newId, tab) => void` | ❌ | Callback após converter |

#### Retorno

| Propriedade | Tipo | Descrição |
|-------------|------|-----------|
| `tabKey` | `Ref<string \| null>` | Chave da tab atual |
| `currentTab` | `Ref<Tab \| null>` | Tab atual |
| `isInitializing` | `Ref<boolean>` | Se está inicializando |
| `updateTabTitle` | `(form) => void` | Atualiza título da tab |
| `saveFormData` | `(form) => void` | Salva dados no localStorage |
| `loadFormData` | `() => any \| null` | Carrega dados salvos |
| `clearFormData` | `() => void` | Limpa dados salvos |
| `convertToEdit` | `(id, title?) => Promise<void>` | Converte create → edit |
| `tabExists` | `() => boolean` | Verifica se tab existe |
| `recreateTab` | `() => void` | Força recriação da tab |

---

### `createTabConfig(config)`

Cria uma configuração de tab validada.

```typescript
const tab = createTabConfig({
  componentName: 'UsersForm',
  context: 'users',
  mode: 'create',
  tempKey: 'temp-123',
  title: 'Novo Usuário',
})
```

---

### `useTabsStore`

#### Métodos

| Método | Descrição |
|--------|-----------|
| `addTab(tab)` | Adiciona uma nova tab |
| `setActive(tab)` | Ativa uma tab |
| `closeTab(tab)` | Fecha uma tab |
| `registerHooks(componentName, hooks)` | Registra hooks para componente |
| `unregisterHooks(componentName)` | Remove hooks |
| `getHooks(componentName)` | Obtém hooks |

---

## 🔧 Casos de Uso Avançados

### 1. Validação Antes de Fechar Tab

```typescript
const tabsStore = useTabsStore()

tabsStore.registerHooks('UsersForm', {
  beforeClose: async (tab) => {
    const hasChanges = checkFormChanges()
    if (hasChanges) {
      const result = await showConfirmDialog({
        title: 'Alterações não salvas',
        message: 'Você tem alterações não salvas. Deseja fechar mesmo assim?',
      })
      return result
    }
    return true
  },
})
```

### 2. Auto-save Periódico

```typescript
const { saveFormData } = useTabForm({...})

// Auto-save a cada 30 segundos
setInterval(() => {
  saveFormData(form.value)
}, 30000)
```

### 3. Sincronização entre Tabs

```typescript
const tabsStore = useTabsStore()

tabsStore.registerHooks('UsersForm', {
  onActivate: (tab) => {
    // Recarrega dados quando tab é ativada
    loadDataFromServer(tab.props.id)
  },
})
```

### 4. Ações Customizadas ao Fechar

```typescript
tabsStore.registerHooks('UsersForm', {
  beforeClose: async (tab) => {
    // Limpa dados temporários
    await cleanupTempData(tab.key)
    return true
  },
})
```

---

## 🐛 Troubleshooting

### Problema: Tab não aparece

**Solução:**
1. Verifique se o componente está registrado em `tabComponents.ts`
2. Verifique se o `componentName` está correto (case-sensitive)
3. Verifique se a tab foi criada com `tabsStore.addTab()` ou `useTabForm()`

### Problema: Erro "Componente não está registrado"

**Solução:**
```typescript
// No arquivo tabComponents.ts
registerTabComponent('SeuComponente', () => import('@/Pages/SeuComponente/Form.vue'))
```

### Problema: Dados não estão sendo salvos

**Solução:**
```typescript
// Use watch para salvar automaticamente
watch(form, (newForm) => {
  saveFormData(newForm)
}, { deep: true })
```

### Problema: Título não atualiza

**Solução:**
```typescript
// Chame updateTabTitle após mudar o formulário
watch(form, (newForm) => {
  updateTabTitle(newForm)
}, { deep: true })
```

### Problema: Tab não fecha quando cria novo registro

**Solução:**
```typescript
// Use convertToEdit após criar
await convertToEdit(data.id, form.value.name)
router.visit(`/users/${data.id}/edit`)
```

---

## ✅ Boas Práticas

### 1. Sempre use `useTabForm`

❌ **Não faça:**
```typescript
// Criar tab manualmente
const tabsStore = useTabsStore()
tabsStore.addTab({...})
```

✅ **Faça:**
```typescript
// Use o composable
const { tabKey, updateTabTitle } = useTabForm({...})
```

### 2. Salve dados automaticamente

✅ **Use watch:**
```typescript
watch(form, (newForm) => {
  saveFormData(newForm)
  updateTabTitle(newForm)
}, { deep: true })
```

### 3. Atualize título dinamicamente

✅ **Sempre atualize quando o formulário muda:**
```typescript
watch(form, (newForm) => {
  updateTabTitle(newForm)
}, { deep: true })
```

### 4. Use callbacks para validações

✅ **Use hooks para validações:**
```typescript
tabsStore.registerHooks('UsersForm', {
  beforeClose: async (tab) => {
    // Sua validação
    return canClose
  },
})
```

### 5. Limpe dados ao converter create → edit

✅ **Use convertToEdit:**
```typescript
await convertToEdit(data.id, form.value.name)
```

### 6. Trate erros adequadamente

✅ **Sempre trate erros:**
```typescript
try {
  await handleSave()
} catch (error) {
  toast.error(error.message)
  console.error('Erro:', error)
}
```

---

## 📖 Checklist de Implementação

Use este checklist ao criar um novo módulo:

- [ ] Componente de formulário criado (`Form.vue`)
- [ ] Componente registrado em `tabComponents.ts`
- [ ] Rotas criadas no backend (`web.php`)
- [ ] Controller criado com métodos `create` e `edit`
- [ ] `useTabForm` configurado no componente
- [ ] Função `getTitle` implementada
- [ ] Watch configurado para salvar dados
- [ ] Watch configurado para atualizar título
- [ ] Carregamento de dados implementado (modo edit)
- [ ] Função de salvar implementada
- [ ] Conversão create → edit implementada (se aplicável)
- [ ] Validação antes de fechar (se necessário)
- [ ] Tratamento de erros implementado
- [ ] Página de listagem criada (se aplicável)
- [ ] Botões de criar/editar na listagem

---

## 🎓 Exemplo Completo

Veja o exemplo completo em `resources/js/Pages/Users/Form.vue` (se existir) ou use como base os formulários existentes:

- `resources/js/Pages/DocumentTypes/Form.vue`
- `resources/js/Pages/DocumentTemplates/Form.vue`

---

## 📞 Suporte

Se tiver dúvidas ou problemas:

1. Consulte a documentação em `resources/js/docs/TABS_USAGE_GUIDE.md`
2. Veja os exemplos em `resources/js/Pages/DocumentTypes/Form.vue`
3. Verifique o código em `resources/js/composables/useTabForm.ts`

---

**Última atualização:** 2025-11-05  
**Versão do sistema:** 1.0

