# Plugins UI Globais

Arquivo central para gerenciar componentes UI globalmente.

## Localização
`resources/js/plugins/ui.js`

## Propósito
- Centralizar inicialização de componentes UI (dropdown, collapse, modal, tooltip, etc)
- Substituir funcionalidades do template Ynex/Preline convertendo para Vue.js
- Evitar código duplicado em componentes individuais
- Funcionar globalmente em SPA (Inertia.js)

## Uso

### Inicialização Global (app.js)
```javascript
import { initAllUIPlugins, reinitUIPlugins } from '@/plugins/ui'

// Inicializa na montagem
initAllUIPlugins()

// Reinicializa após navegação Inertia
router.on('navigate', () => {
  reinitUIPlugins()
})
```

### Inicialização Manual (componentes)
```javascript
import { initDropdownGlobal, initCollapseGlobal } from '@/plugins/ui'

onMounted(() => {
  initDropdownGlobal()
})
```

## Funções Disponíveis

### `initAllUIPlugins()`
Inicializa TODOS os plugins UI globalmente.
- Aguarda DOM estar pronto
- Usado no app.js

### `reinitUIPlugins()`
Reinicializa plugins após navegação SPA.
- Delay de 50ms para garantir DOM atualizado
- Usado no router.on('navigate')

### `initDropdownGlobal()`
Inicializa dropdowns globalmente.
- Adiciona classe `open` no `.hs-dropdown` quando clicado
- Fecha outros dropdowns ao abrir um novo
- ESC fecha todos os dropdowns
- Click fora fecha dropdown

**Estrutura HTML esperada:**
```html
<div class="hs-dropdown">
  <button class="hs-dropdown-toggle">
    Clique aqui
  </button>
  <div class="ti-dropdown-menu">
    Conteúdo do dropdown
  </div>
</div>
```

**Como funciona:**
1. Busca `.hs-dropdown-toggle` no document
2. Ao clicar, busca `.hs-dropdown` pai
3. Adiciona classe `open` (CSS controla visibilidade)
4. ESC ou click fora fecha

### `initCollapseGlobal()`
TODO: Implementar

### `initModalGlobal()`
TODO: Implementar

## CSS Necessário
O CSS do template já existe em `resources/assets/css/style.css`:

```css
.ti-dropdown-menu {
  opacity: 0;
  transition: opacity 150ms;
}

.hs-dropdown.open > .ti-dropdown-menu {
  opacity: 1;
}
```

## Diferença: Global vs Composable

### Plugin Global (ui.js)
- ✅ Inicializa automaticamente
- ✅ Funciona em TODA a aplicação
- ✅ Não precisa importar em componentes
- ✅ Ideal para funcionalidades básicas (dropdown, collapse)

### Composable (useDropdown.js)
- ✅ Controle fino por componente
- ✅ Acesso a funções específicas (closeAllDropdowns)
- ✅ Lifecycle do Vue (onMounted, onUnmounted)
- ✅ Ideal para funcionalidades customizadas

## Quando Usar Cada Um?

### Use Plugin Global quando:
- Funcionalidade básica (dropdown, collapse, tooltip)
- Usado em múltiplos componentes
- Não precisa de customização

### Use Composable quando:
- Precisa de controle fino
- Lógica específica do componente
- Precisa chamar funções manualmente (ex: closeAllDropdowns)

## Próximas Conversões

Funções do Ynex para converter:
1. ✅ Dropdown (concluído)
2. ⏳ Collapse (pendente)
3. ⏳ Modal (pendente)
4. ⏳ Tooltip (pendente - temos composable, falta global)
5. ⏳ Tabs (pendente)
6. ⏳ Accordion (pendente)
7. ⏳ Offcanvas (pendente)

## Estrutura do Template Ynex

Arquivos JavaScript originais em `Ynex/resources/assets/js/`:
- `custom.js` - Funcionalidades customizadas (scroll to top, box controls, etc)
- `main.js` - Inicialização de tema e localStorage
- `defaultmenu.js` - Menu sidebar
- `custom-switcher.js` - Tema switcher
- `modal.js` - Modals
- E mais ~150 arquivos

## Notas

- **Cleanup automático**: Handlers são armazenados em `window.__*Handler` para cleanup
- **SPA-friendly**: Reinicializa após navegação Inertia
- **Performance**: Usa event delegation (um listener para todos os dropdowns)
- **Acessibilidade**: Suporta ESC para fechar, aria-labelledby

## Troubleshooting

### Dropdown não abre
1. Verifique se `initAllUIPlugins()` foi chamado no app.js
2. Verifique estrutura HTML (`.hs-dropdown` > `.hs-dropdown-toggle`)
3. Verifique CSS (classe `open` deve adicionar `opacity: 1`)
4. Verifique console (logs de inicialização)

### Dropdown não fecha
1. ESC deve fechar todos
2. Click fora deve fechar
3. Verificar se `clickHandler` está registrado

### Dropdown não funciona após navegação
1. Verificar se `reinitUIPlugins()` está no router.on('navigate')
2. Aumentar delay se necessário (padrão: 50ms)
