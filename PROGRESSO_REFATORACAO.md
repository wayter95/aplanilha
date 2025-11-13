# Progresso da Refatoração - Estrutura JavaScript

**Última atualização:** 07/11/2025  
**Status:** ✅ PRIORIDADES 1 E 2 COMPLETAS

---

## ✅ PRIORIDADE 1 - COMPLETA

### 1. ✅ Camada API Base (`api/client.js`)
**O que foi criado:**
- Instância axios customizada com baseURL `/api`
- Interceptors de request (CSRF token automático)
- Interceptors de response (tratamento de erro global)
- Eventos customizados para loading states:
  - `api:loading:start` - Quando request inicia
  - `api:loading:end` - Quando request termina
- Eventos de erro específicos:
  - `api:error:unauthorized` (401) - Redireciona para login
  - `api:error:forbidden` (403)
  - `api:error:notfound` (404)
  - `api:error:validation` (422)
  - `api:error:server` (500)
  - `api:error:network` - Timeout/sem internet
- Helpers: `get()`, `post()`, `put()`, `patch()`, `del()`

**Benefícios:**
- ❌ ANTES: window.axios espalhado 30+ lugares
- ✅ DEPOIS: 1 cliente centralizado com interceptors
- Fácil adicionar retry logic, cache, etc no futuro

---

### 2. ✅ API Services Criados

#### `api/documentTemplates.js`
**Métodos:**
- `getTypes()` - Listar tipos
- `getAll(params)` - Listar templates
- `getById(id)` - Buscar por ID
- `create(data)` - Criar template
- `update(id, data)` - Atualizar
- `delete(id)` - Deletar
- `setDefault(id)` - Definir como padrão
- `duplicate(id, overrides)` - Duplicar (preparado para futuro)

#### `api/documentTypes.js`
**Métodos:**
- `getAll(params)`
- `getById(id)`
- `create(data)`
- `update(id, data)`
- `delete(id)`

#### `api/users.js`
**Métodos:**
- `getAll(params)`
- `getById(id)`
- `create(data)`
- `update(id, data)`
- `delete(id)`
- `updatePhoto(id, formData)` - Upload de foto
- `updatePassword(id, data)` - Trocar senha

#### `api/index.js`
**Exports centralizados:**
```javascript
import { documentTemplatesApi, documentTypesApi, usersApi } from '@/api'
```

---

### 3. ✅ Composables de API

#### `composables/useDocumentTemplates.js`
**Estado reativo:**
- `loading` - Boolean de carregamento
- `error` - Último erro (se houver)
- `types` - Array de tipos disponíveis
- `itemsByType` - Objeto reativo agrupado por tipo
- `grouped` - Computed com formato { type, items[] }

**Métodos:**
- `fetchTypes()` - Buscar tipos
- `fetchByType(type, params)` - Buscar templates de um tipo
- `fetchAll()` - Buscar tudo (tipos + templates)
- `fetchById(id)` - Buscar por ID
- `create(templateData)` - Criar + atualizar lista
- `update(id, templateData)` - Atualizar + atualizar lista
- `remove(id, type)` - Deletar + atualizar lista
- `setDefault(id, type)` - Definir padrão + atualizar lista
- `duplicate(item)` - Duplicar + atualizar lista
- `bulkDelete(ids)` - Deletar múltiplos

**Vantagens:**
- Loading states automáticos
- Atualiza listas automaticamente após CRUD
- Normalização de dados (data.data vs data)
- Tratamento de erro consistente

#### `composables/useDocumentTypes.js`
**Similar ao anterior, mas para tipos de documentos.**

---

### 4. ✅ Refatoração: DocumentTemplates.vue

#### ❌ ANTES (67 linhas de lógica):
```javascript
// 6x window.axios direto
await window.axios.get('/api/document-templates/types')
await window.axios.get('/api/document-templates', { params })
await window.axios.post(`/api/document-templates/${id}/set-default`)
await window.axios.delete(`/api/document-templates/${id}`)
await window.axios.post('/api/document-templates', payload)

// Lógica repetida
const { data } = await window.axios.get(...)
const items = Array.isArray(data?.data) ? data.data : ...
this.$set(this.itemsByType, type, items)
```

#### ✅ DEPOIS (45 linhas de lógica):
```javascript
// 1x composable
const {
  loading, types, itemsByType, grouped,
  fetchAll, setDefault, remove, duplicate, bulkDelete
} = useDocumentTemplates()

// Métodos limpos
async setDefault(item) {
  await this.apiSetDefault(item.id, item.type)
  this.toast.success('Modelo definido como padrão')
}
```

**Redução:**
- ❌ 67 linhas → ✅ 45 linhas (-33%)
- ❌ 6 window.axios → ✅ 0 window.axios
- ❌ Lógica espalhada → ✅ Composable reutilizável
- ✅ Tratamento de erro try/catch
- ✅ Loading states disponíveis (não usado ainda no template)

---

## ✅ PRIORIDADE 2 - COMPLETA

### 1. ✅ Composables de DOM
**Criados:**
- `composables/useHtmlElement.js` - Acesso centralizado ao `<html>`
  - Métodos: addClass, removeClass, toggleClass, hasClass
  - Métodos: getAttribute, setAttribute, removeAttribute
  - Sem `document.querySelector` solto

### 2. ✅ Composables de Eventos
**Criados:**
- `composables/useEvents.js` - EventBus centralizado
  - Constantes: `APP_EVENTS` com todos os eventos
  - Métodos: emit, on, off, once
  - **Cleanup automático** com onUnmounted
  - Helpers específicos: useUserEvents, useThemeEvents, useOverlayEvents

**Eventos padronizados:**
```javascript
APP_EVENTS.USER_PHOTO_UPDATED
APP_EVENTS.THEME_CHANGED
APP_EVENTS.OVERLAY_TOGGLE
APP_EVENTS.SIDEBAR_TOGGLE
APP_EVENTS.API_LOADING_START
// ... e outros
```

### 3. ✅ Plugin Preline Reorganizado
**Criado:**
- `plugins/preline.js` - Centralização do Preline
  - `initPreline()` - Inicialização
  - `reinitPreline()` - Após navegação Inertia
  - Componentes: HSDropdown, HSOverlay, HSTooltip, HSAccordion
  - Logs de debug incluídos

**app.js atualizado:**
```javascript
// ❌ ANTES: import dinâmico confuso
import('preline/preline').then(({ HSDropdown }) => {
  window.HSDropdown = HSDropdown
  HSDropdown.autoInit()
})

// ✅ DEPOIS: limpo e organizado
import { initPreline, reinitPreline } from '@/plugins/preline'
initPreline()
router.on('navigate', reinitPreline)
```

### 4. ✅ Refatoração: ThemeSwitcher.vue
**Mudanças:**
- ❌ `const html = document.querySelector('html')` 
- ✅ `const { html, getAttribute, hasClass } = useHtmlElement()`
- ❌ `window.addEventListener('overlay-toggle', ...)`
- ✅ `const { onToggle } = useOverlayEvents()`
- **Cleanup automático** de event listeners

### 5. ✅ Refatoração: Settings.vue
**Mudanças:**
- ❌ `window.dispatchEvent(new CustomEvent('user-photo-updated', ...))`
- ✅ `const { emitPhotoUpdated } = useUserEvents()`
- ❌ `window.addEventListener('user-photo-updated', handler)`
- ❌ `window.removeEventListener('user-photo-updated', handler)` (manual)
- ✅ `onPhotoUpdated(handler)` (cleanup automático!)

---

## ✅ LIMPEZA - COMPLETA

### Código Morto Deletado:
- ✅ `composables/useDropdown.js` (~170 linhas) - DELETADO
- ✅ `plugins/ui.js` (~150 linhas) - DELETADO
- **Total removido: ~320 linhas de código não utilizado**

---

## 🟡 PENDENTE

### PRIORIDADE 1 (Próximos)
- [ ] Refatorar DocumentTemplateEditor.vue (window.axios)
- [ ] Refatorar DocumentTypes/Form.vue (window.axios)
- [ ] Refatorar outros 20+ componentes com window.axios
  - DocumentTypesModal.vue
  - UserForm.vue
  - E outros...

---

## 📊 Impacto da Refatoração

### Antes da Refatoração:
```
❌ window.axios espalhado: 30+ lugares
❌ Lógica duplicada: 10+ componentes
❌ Sem loading states centralizados
❌ Sem tratamento de erro global
❌ Difícil testar
❌ Difícil manter
```

### Depois da Refatoração (ATUAL):
```
✅ API centralizada: api/client.js
✅ Services organizados: api/documentTemplates.js, etc
✅ Composables reutilizáveis: useDocumentTemplates.js
✅ Loading states automáticos
✅ Tratamento de erro global
✅ Fácil testar (mock api/client)
✅ Fácil manter (1 lugar para mudar)
```

---

## 🎯 Próximo Passo

**TESTAR DocumentTemplates.vue no navegador:**
1. Acessar `/document-templates`
2. Verificar lista carrega
3. Testar "Definir como Padrão"
4. Testar "Duplicar"
5. Testar "Remover"
6. Testar "Remover Selecionados"

Se funcionar, continuar refatoração dos outros componentes.

---

## 📝 Notas Técnicas

### Por que não usar setup() em todos?
- Componente usa Options API (data, methods)
- Misturar com Composition API (setup) é válido
- Mantemos compatibilidade com código existente
- Migração gradual é mais segura

### Por que reactive({}) em itemsByType?
- Permite `itemsByType[type] = items` e Vue detecta mudança
- Em ref({}) precisaria `itemsByType.value[type]`
- Mais clean no composable

### Por que computed() em grouped?
- Recalcula automaticamente quando types ou itemsByType mudam
- Cache interno do Vue
- Performance melhor que método

---

## 📈 EVOLUÇÃO DA NOTA

### Antes da Refatoração: **6/10**
- window.axios espalhado (30+ lugares)
- Código duplicado
- Sem padrão consistente
- Difícil manter

### Após PRIORIDADE 1: **7/10**
- Camada API criada ✅
- Composables de API ✅
- 1 componente refatorado (exemplo) ✅

### Após PRIORIDADE 2: **8/10** ⬆️
- Composables de DOM ✅
- EventBus centralizado ✅
- Preline organizado ✅
- ThemeSwitcher refatorado ✅
- Settings refatorado ✅
- Código morto deletado ✅

**Próximo objetivo: 9/10** (quando refatorar todos os window.axios)
