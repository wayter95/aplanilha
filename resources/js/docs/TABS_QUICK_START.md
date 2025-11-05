# ⚡ Quick Start: Sistema de Tabs

Guia rápido para adicionar tabs em um novo módulo em **5 minutos**.

---

## 🎯 Passos Rápidos

### 1. Registrar Componente (30 segundos)

No arquivo `resources/js/config/tabComponents.ts`:

```typescript
registerTabComponent('SeuModuloForm', () => import('@/Pages/SeuModulo/Form.vue'))
```

### 2. Criar Componente (2 minutos)

Crie `resources/js/Pages/SeuModulo/Form.vue`:

```vue
<template>
  <div class="p-6">
    <Form @submit="handleSave">
      <Input name="name" label="Nome" v-model="form.name" />
      <Button type="submit">Salvar</Button>
    </Form>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useTabForm } from '@/composables/useTabForm'

const props = defineProps({
  mode: { type: String, default: 'create' },
  id: String,
  tempKey: String,
})

const form = ref({ name: '' })

// ⚡ CONFIGURAÇÃO DO SISTEMA DE TABS (3 linhas!)
const { saveFormData, updateTabTitle, convertToEdit } = useTabForm({
  componentName: 'SeuModuloForm',
  context: 'seu-modulo',
  mode: props.mode,
  id: props.id,
  tempKey: props.tempKey,
  getTitle: (form) => form.name || 'Novo Item',
})

// ⚡ Salva automaticamente (2 linhas!)
watch(form, (newForm) => {
  saveFormData(newForm)
  updateTabTitle(newForm)
}, { deep: true })

async function handleSave() {
  if (props.mode === 'create') {
    const { data } = await axios.post('/api/seu-modulo', form.value)
    await convertToEdit(data.id, form.value.name)
    router.visit(`/seu-modulo/${data.id}/edit`)
  } else {
    await axios.put(`/api/seu-modulo/${props.id}`, form.value)
  }
}
</script>
```

### 3. Criar Rotas (1 minuto)

No `routes/web.php`:

```php
Route::get('/seu-modulo/new/{tempKey?}', [SeuModuloController::class, 'create']);
Route::get('/seu-modulo/{id}/edit', [SeuModuloController::class, 'edit']);
```

### 4. Criar Controller (1 minuto)

```php
public function create($tempKey = null) {
    if (!$tempKey) {
        $tempKey = 'temp-' . time();
        return redirect()->route('seu-modulo.create', ['tempKey' => $tempKey]);
    }
    
    return Inertia::render('SeuModulo/Form', [
        'mode' => 'create',
        'tempKey' => $tempKey,
    ]);
}

public function edit($id) {
    return Inertia::render('SeuModulo/Form', [
        'mode' => 'edit',
        'id' => $id,
    ]);
}
```

### 5. Abrir Tab na Listagem (30 segundos)

```vue
<script setup>
import { useTabsStore } from '@/stores/useTabsStore'
import { createTabConfig, generateTempKey } from '@/utils/tabHelpers'

const tabsStore = useTabsStore()

function openCreateTab() {
  const tab = createTabConfig({
    componentName: 'SeuModuloForm',
    context: 'seu-modulo',
    mode: 'create',
    tempKey: generateTempKey(),
  })
  tabsStore.addTab(tab)
  router.visit(`/seu-modulo/new/${tab.key}`)
}
</script>
```

---

## ✅ Pronto!

Agora você tem:
- ✅ Tab funcionando
- ✅ Salvamento automático
- ✅ Título dinâmico
- ✅ Conversão create → edit

**Total:** ~30 linhas de código!

---

## 📚 Documentação Completa

Para mais detalhes, consulte:
- `TABS_COMPLETE_GUIDE.md` - Guia completo
- `TABS_USAGE_GUIDE.md` - Referência de API

