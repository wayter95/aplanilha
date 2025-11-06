# 📚 Guia de Uso do Sistema de Tabs

## 🎯 Visão Geral

O sistema de tabs foi refatorado para ser **robusto, escalável e reutilizável**. Agora você pode adicionar novos formulários sem editar o `AppLayout` ou repetir código.

---

## 🚀 Como Adicionar um Novo Formulário

### Passo 1: Registrar o Componente

No arquivo `resources/js/config/tabComponents.ts`:

```typescript
import { registerTabComponent } from '@/config/tabComponents'

// Registrar seu componente
registerTabComponent('UsersForm', () => import('@/Pages/Users/Form.vue'))
```

**Ou adicione diretamente no arquivo:**

```typescript
// resources/js/config/tabComponents.ts
registerTabComponent('UsersForm', () => import('@/Pages/Users/Form.vue'))
registerTabComponent('RolesForm', () => import('@/Pages/Roles/Form.vue'))
```

---

### Passo 2: Usar o Composable no Formulário

No seu formulário Vue (`resources/js/Pages/Users/Form.vue`):

```vue
<script setup>
import { useTabForm } from '@/composables/useTabForm'
import { ref } from 'vue'

const props = defineProps({
  mode: { type: String, default: 'create' },
  id: String,
  tempKey: String,
})

const form = ref({
  name: '',
  email: '',
  // ... outros campos
})

// Usa o composable
const {
  tabKey,
  isInitializing,
  updateTabTitle,
  saveFormData,
  loadFormData,
  convertToEdit,
} = useTabForm({
  componentName: 'UsersForm',
  context: 'users',
  mode: props.mode,
  id: props.id,
  tempKey: props.tempKey,
  getTitle: (form, mode) => {
    if (mode === 'edit') {
      return form.name || 'Usuário'
    }
    return 'Novo Usuário'
  },
  defaultCreateTitle: 'Novo Usuário',
  defaultEditTitle: 'Carregando…',
})

// Carrega dados salvos ao montar
const savedData = loadFormData()
if (savedData) {
  Object.assign(form.value, savedData)
}

// Salva dados ao mudar formulário
watch(form, (newForm) => {
  saveFormData(newForm)
  updateTabTitle(newForm)
}, { deep: true })

// Ao salvar (criar)
async function handleSave() {
  const { data } = await axios.post('/api/users', form.value)
  
  if (props.mode === 'create') {
    // Converte create → edit
    await convertToEdit(data.id, form.value.name)
    router.visit(`/users/${data.id}/edit`)
  }
}
</script>
```

---

## 📖 API do Composable `useTabForm`

### Configuração

```typescript
interface UseTabFormConfig {
  componentName: string      // Nome do componente (deve estar registrado)
  context: string            // Contexto (ex: 'users', 'document-types')
  mode: 'create' | 'edit'    // Modo da tab
  id?: string                // ID (obrigatório para edit)
  tempKey?: string           // Chave temporária (obrigatório para create)
  getTitle: (form, mode) => string  // Função para gerar título
  defaultCreateTitle?: string
  defaultEditTitle?: string
  props?: Record<string, any>
  onTabCreated?: (tab) => void
  onTabUpdated?: (tab) => void
  beforeConvertToEdit?: (tempKey, newId) => boolean | Promise<boolean>
  afterConvertToEdit?: (tempKey, newId, tab) => void
}
```

### Retorno

```typescript
interface UseTabFormReturn {
  tabKey: Ref<string | null>           // Chave da tab atual
  currentTab: Ref<Tab | null>          // Tab atual
  isInitializing: Ref<boolean>         // Se está inicializando
  updateTabTitle: (form) => void       // Atualiza título
  saveFormData: (form) => void         // Salva dados
  loadFormData: () => any | null       // Carrega dados salvos
  clearFormData: () => void            // Limpa dados
  convertToEdit: (id, title?) => Promise<void>  // Converte create → edit
  tabExists: () => boolean             // Verifica se tab existe
  recreateTab: () => void              // Força recriação
}
```

---

## 🎣 Sistema de Hooks

Você pode registrar hooks para interceptar eventos de tabs:

```typescript
import { useTabsStore } from '@/stores/useTabsStore'

const tabsStore = useTabsStore()

// Registrar hooks
tabsStore.registerHooks('UsersForm', {
  beforeClose: async (tab) => {
    // Valida se pode fechar
    const hasChanges = checkFormChanges()
    if (hasChanges) {
      const confirm = await showConfirmDialog('Tem alterações não salvas. Fechar?')
      return confirm
    }
    return true
  },
  
  afterCreate: (tab) => {
    console.log('Tab criada:', tab)
  },
  
  onActivate: (tab) => {
    console.log('Tab ativada:', tab)
  },
  
  onDeactivate: (tab) => {
    console.log('Tab desativada:', tab)
  },
})
```

---

## 🛠️ Helpers Disponíveis

### `createTabConfig()`

Cria uma configuração de tab validada:

```typescript
import { createTabConfig } from '@/utils/tabHelpers'

const tab = createTabConfig({
  componentName: 'UsersForm',
  context: 'users',
  mode: 'create',
  tempKey: 'temp-123',
  title: 'Novo Usuário',
})
```

### `generateTempKey()`

Gera uma chave temporária única:

```typescript
import { generateTempKey } from '@/utils/tabHelpers'

const tempKey = generateTempKey() // 'temp-1234567890-abc123'
```

### `validateTab()`

Valida se uma tab está bem formada:

```typescript
import { validateTab } from '@/utils/tabHelpers'

if (validateTab(tab)) {
  // Tab válida
}
```

---

## 📝 Exemplo Completo

### Formulário de Usuários

```vue
<template>
  <AppLayout :title="computedTitle" :user="user">
    <Form @submit="handleSave" :initial-values="form">
      <Input name="name" label="Nome" v-model="form.name" />
      <Input name="email" label="Email" v-model="form.email" />
      <Button type="submit">Salvar</Button>
    </Form>
  </AppLayout>
</template>

<script setup>
import { useTabForm } from '@/composables/useTabForm'
import { ref, watch, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
  mode: { type: String, default: 'create' },
  id: String,
  tempKey: String,
})

const page = usePage()
const user = computed(() => page.props.user)

const form = ref({
  name: '',
  email: '',
})

// Usa o composable
const {
  tabKey,
  isInitializing,
  updateTabTitle,
  saveFormData,
  loadFormData,
  convertToEdit,
} = useTabForm({
  componentName: 'UsersForm',
  context: 'users',
  mode: props.mode,
  id: props.id,
  tempKey: props.tempKey,
  getTitle: (form, mode) => {
    return mode === 'edit' ? (form.name || 'Usuário') : 'Novo Usuário'
  },
})

// Carrega dados salvos
const savedData = loadFormData()
if (savedData) {
  Object.assign(form.value, savedData)
}

// Carrega dados do servidor (modo edit)
if (props.mode === 'edit' && props.id) {
  // Carregar do servidor...
}

// Salva dados ao mudar
watch(form, (newForm) => {
  saveFormData(newForm)
  updateTabTitle(newForm)
}, { deep: true })

const computedTitle = computed(() => {
  return props.mode === 'edit' ? (form.value.name || 'Usuário') : 'Novo Usuário'
})

async function handleSave() {
  if (props.mode === 'create') {
    const { data } = await axios.post('/api/users', form.value)
    await convertToEdit(data.id, form.value.name)
    router.visit(`/users/${data.id}/edit`)
  } else {
    await axios.put(`/api/users/${props.id}`, form.value)
  }
}
</script>
```

---

## ✅ Benefícios

1. **Código reduzido em 70-80%** - Não precisa repetir lógica
2. **Type-safe** - TypeScript valida tudo
3. **Reutilizável** - Funciona para qualquer formulário
4. **Manutenível** - Mudanças centralizadas
5. **Escalável** - Fácil adicionar novos formulários

---

## 🐛 Troubleshooting

### Erro: "Componente não está registrado"

**Solução:** Registre o componente em `tabComponents.ts`:

```typescript
registerTabComponent('SeuComponente', () => import('@/Pages/SeuComponente/Form.vue'))
```

### Tab não está aparecendo

**Solução:** Verifique se:
1. O componente está registrado
2. O `componentName` está correto
3. A tab foi criada com `useTabForm` ou `tabsStore.addTab()`

### Dados não estão sendo salvos

**Solução:** Use `saveFormData()` do composable:

```typescript
watch(form, (newForm) => {
  saveFormData(newForm)
}, { deep: true })
```

---

## 📚 Referências

- `resources/js/config/tabComponents.ts` - Registro de componentes
- `resources/js/composables/useTabForm.ts` - Composable principal
- `resources/js/utils/tabHelpers.ts` - Helpers
- `resources/js/stores/useTabsStore.ts` - Store Pinia
- `resources/js/stores/useTabFormDataStore.ts` - Store de dados

