# Estrutura de Composables e Utils - Guia de Uso

## 📁 Estrutura Organizada

```
resources/js/
├── utils/
│   ├── localStorage.js  → Gerenciamento centralizado de localStorage
│   └── dom.js          → Helpers para manipulação DOM
├── composables/
│   ├── useRouterEvents.js   → Eventos do Inertia Router
│   ├── useSidebarToggle.js  → Controle da sidebar (refatorado)
│   ├── useThemeConfig.js    → Configurações de tema
│   ├── useLoader.js         → Page loader
│   └── ... (outros)
└── Components/
    └── ... (componentes Vue)
```

---

## 🛠️ Utils Criados

### 1. `utils/localStorage.js`
**Centraliza todas as operações de localStorage**

```javascript
import { getItem, setItem, removeItem, getJSON, setJSON, has } from '@/utils/localStorage'

// Uso básico
setItem('key', 'value')
const value = getItem('key')
removeItem('key')

// JSON
setJSON('config', { theme: 'dark' })
const config = getJSON('config', { theme: 'light' }) // com default

// Verificar existência
if (has('user')) { ... }
```

**Benefícios:**
- ✅ Try/catch automático
- ✅ Logs de erro centralizados
- ✅ Suporte JSON nativo
- ✅ Default values

---

### 2. `utils/dom.js`
**Centraliza manipulação DOM**

```javascript
import { getHtml, getElement, setAttr, getAttr, removeAttr, addClass, removeClass } from '@/utils/dom'

// HTML element
const html = getHtml()

// Atributos
setAttr(html, 'data-theme', 'dark')
const theme = getAttr(html, 'data-theme')
removeAttr(html, 'data-theme')

// Classes
addClass(element, 'active', 'visible')
removeClass(element, 'hidden')
toggleClass(element, 'open')

// Estilos
setStyle(html, '--primary', '#ff0000')
```

**Benefícios:**
- ✅ Null-safe (retorna false se element não existe)
- ✅ API consistente
- ✅ Fácil debugar

---

### 3. `composables/useRouterEvents.js`
**Gerencia eventos do Inertia Router**

```javascript
import { useRouterEvents } from '@/composables/useRouterEvents'

const { onStart, onFinish, onNavigate } = useRouterEvents()

onStart(() => {
  console.log('Navigation started')
})

onFinish(() => {
  console.log('Navigation finished')
})

onNavigate(() => {
  console.log('Page changed')
})
```

**Eventos disponíveis:**
- `onStart` - Navegação começou
- `onFinish` - Navegação terminou
- `onNavigate` - Página mudou
- `onSuccess` - Request bem sucedido
- `onError` - Erro na request
- `onBefore` - Antes de navegar
- `onProgress` - Progresso do upload

**Benefícios:**
- ✅ Auto-cleanup em `onUnmounted`
- ✅ Múltiplos callbacks por evento
- ✅ API consistente

---

## 🔄 Composables Refatorados

### `useSidebarToggle.js`

**ANTES:**
```javascript
const html = document.querySelector('html')
html.setAttribute('data-toggled', 'close')
localStorage.setItem('ynextoggledSidebar', 'close')
```

**DEPOIS:**
```javascript
import { getHtml, setAttr } from '@/utils/dom'
import { setItem } from '@/utils/localStorage'

const html = getHtml()
setAttr(html, 'data-toggled', 'close')
setItem('ynextoggledSidebar', 'close')
```

**Benefícios:**
- ✅ Código mais legível
- ✅ Null-safe automático
- ✅ Error handling centralizado
- ✅ Fácil testar

---

## 📝 Padrões de Uso

### ❌ NÃO FAZER (Código duplicado)
```javascript
// Em vários arquivos
const html = document.querySelector('html')
html.setAttribute('data-theme', 'dark')
localStorage.setItem('theme', 'dark')
```

### ✅ FAZER (Usar utils)
```javascript
import { getHtml, setAttr } from '@/utils/dom'
import { setItem } from '@/utils/localStorage'

setAttr(getHtml(), 'data-theme', 'dark')
setItem('theme', 'dark')
```

---

## 🎯 Quando Usar Cada Um

| Necessidade | Usar |
|------------|------|
| Salvar/carregar localStorage | `utils/localStorage` |
| Manipular atributos HTML | `utils/dom` |
| Listeners do router | `useRouterEvents` |
| Controlar sidebar | `useSidebarToggle` |
| Tema/dark mode | `useTheme` |
| Fullscreen | `useFullscreen` |
| Dropdowns | `useDropdown` |
| Toasts/notificações | `useToast` |
| Modals/overlays | `useOverlay` |
| Tooltips | `useTooltip` |

---

## 🚀 Próximos Passos

1. **Migrar componentes** para usar novos utils
2. **Refatorar `useThemeConfig`** para usar `utils/localStorage` e `utils/dom`
3. **Atualizar `AppLayout.vue`** para usar `useRouterEvents`
4. **Documentar** componentes que usam os utils

---

## 📚 Exemplos Completos

### Exemplo 1: Salvar configuração de usuário
```javascript
import { setJSON, getJSON } from '@/utils/localStorage'

const saveUserConfig = (config) => {
  setJSON('userConfig', config)
}

const loadUserConfig = () => {
  return getJSON('userConfig', {
    theme: 'light',
    language: 'pt-BR'
  })
}
```

### Exemplo 2: Toggle classe no HTML
```javascript
import { getHtml, toggleClass, hasClass } from '@/utils/dom'

const toggleDarkMode = () => {
  const html = getHtml()
  toggleClass(html, 'dark')
  
  const isDark = hasClass(html, 'dark')
  console.log('Dark mode:', isDark)
}
```

### Exemplo 3: Listener de navegação
```javascript
import { useRouterEvents } from '@/composables/useRouterEvents'

const { onNavigate } = useRouterEvents()

onNavigate(() => {
  // Fechar modals
  // Limpar seleções
  // Restaurar scroll
})
```

---

**Última atualização:** 07/11/2025
