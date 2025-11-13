# Conversão de Dropdown Template → Vue.js

## Problema Original
- Dropdown do Header não funcionava (abria para o lado em vez de para baixo)
- Código do Preline foi removido, mas HTML ainda tinha classes do Preline
- Usuário queria funções globais reutilizáveis, não código solto em componentes

## Solução Implementada

### 1. Investigação do Template Ynex
- Analisamos `Ynex/resources/assets/js/custom.js` e `main.js`
- Descobrimos que o template usa Preline UI (biblioteca HSDropdown)
- Encontramos o CSS em `resources/assets/css/style.css`:
  ```css
  .hs-dropdown.open > .ti-dropdown-menu {
    opacity: 1;
  }
  ```
- Conclusão: Só precisamos adicionar/remover a classe `open` no elemento `.hs-dropdown`

### 2. Arquivo de Plugins Globais
Criado: `resources/js/plugins/ui.js`

**Funções principais:**
- `initDropdownGlobal()` - Inicializa dropdowns globalmente
- `initCollapseGlobal()` - TODO (placeholder)
- `initModalGlobal()` - TODO (placeholder)
- `initAllUIPlugins()` - Inicializa todos os plugins
- `reinitUIPlugins()` - Reinicializa após navegação SPA

**Como funciona:**
1. Event delegation: Um listener para TODOS os dropdowns
2. Click em `.hs-dropdown-toggle` → busca `.hs-dropdown` pai → adiciona classe `open`
3. Click fora ou ESC → fecha todos os dropdowns
4. Cleanup automático para evitar memory leaks

### 3. Integração no app.js
Adicionado:
```javascript
import { initAllUIPlugins, reinitUIPlugins } from '@/plugins/ui'
initAllUIPlugins()

router.on('navigate', () => {
  reinitUIPlugins()
})
```

### 4. Simplificação do Header.vue
**ANTES:**
```javascript
import { useDropdown } from '@/composables/useDropdown'
const { initDropdown } = useDropdown()

onMounted(() => {
  initDropdown()
})
```

**DEPOIS:**
```javascript
// Dropdown agora é global, não precisa importar
onMounted(() => {
  loadUserPhoto()
  initOverlayTriggers()
})
```

### 5. Composable useDropdown.js
- Mantido para casos onde seja necessário controle fino
- Simplificado: apenas adiciona/remove classe `open`
- Pode ser usado em componentes que precisam de lógica customizada

### 6. Documentação
Criado: `resources/js/plugins/README.md`
- Explica quando usar plugin global vs composable
- Lista próximas conversões (collapse, modal, tooltip, tabs, etc)
- Troubleshooting

## Estrutura de Arquivos

```
resources/js/
├── app.js (inicializa plugins globais)
├── plugins/
│   ├── ui.js (⭐ NOVO - funções globais)
│   └── README.md (⭐ NOVO - documentação)
├── composables/
│   ├── useDropdown.js (simplificado)
│   └── ...outros composables
└── Components/
    └── Header.vue (simplificado - sem useDropdown)
```

## Vantagens da Abordagem

### Plugins Globais (ui.js)
✅ Inicialização automática  
✅ Funciona em TODA a aplicação  
✅ Não precisa importar em cada componente  
✅ Ideal para funcionalidades básicas  
✅ Performance (event delegation)  

### Composables (useDropdown.js)
✅ Controle fino quando necessário  
✅ Acesso a funções específicas  
✅ Lifecycle do Vue (onMounted, onUnmounted)  
✅ Ideal para lógica customizada  

## Próximos Passos

### Testar Dropdown
1. Rodar `npm run dev`
2. Acessar aplicação
3. Clicar no dropdown do profile no Header
4. Verificar se abre corretamente (para baixo)
5. Verificar se fecha ao clicar fora
6. Verificar se fecha com ESC

### Converter Mais Funções do Ynex
1. **Collapse** - Para seções expansíveis
2. **Modal** - Para janelas modais
3. **Tooltip** - Já temos composable, criar versão global
4. **Tabs** - Para navegação em abas
5. **Accordion** - Para FAQ/perguntas
6. **Offcanvas** - Para sidebars secundárias

### Refatorações Pendentes
- Refatorar `useThemeConfig.js` com utils/localStorage.js e utils/dom.js
- Criar mais utils conforme necessário
- Documentar padrões de uso

## Padrão Estabelecido

### Para Funcionalidades Globais:
1. Criar função em `plugins/ui.js`
2. Usar event delegation
3. Cleanup automático (window.__*Handler)
4. Inicializar em `app.js`
5. Reinicializar após navegação SPA

### Para Funcionalidades Específicas:
1. Criar composable em `composables/use*.js`
2. Usar utils centralizados (localStorage, dom)
3. Documentar em `composables/README.md`
4. Importar apenas onde necessário

## Checklist de Verificação

- [x] Plugin global criado (`plugins/ui.js`)
- [x] Documentação criada (`plugins/README.md`)
- [x] Integrado no `app.js`
- [x] Header.vue simplificado
- [x] useDropdown.js mantido (para casos customizados)
- [x] Sem erros de compilação
- [ ] **Testar dropdown no navegador** (PRÓXIMO PASSO)

## Notas Técnicas

### CSS do Template
O template já tem CSS pronto:
```css
.ti-dropdown-menu {
  opacity: 0;
  transition: opacity 150ms;
}
.hs-dropdown.open > .ti-dropdown-menu {
  opacity: 1;
}
```

### HTML Necessário
```html
<div class="hs-dropdown">
  <button class="hs-dropdown-toggle">Toggle</button>
  <div class="ti-dropdown-menu">Menu</div>
</div>
```

### JavaScript
```javascript
// Busca toggle
const toggle = event.target.closest('.hs-dropdown-toggle')
// Busca dropdown pai
const dropdown = toggle.closest('.hs-dropdown')
// Adiciona classe
dropdown.classList.add('open')
```

## Comparação: Antes vs Depois

### ANTES (Preline)
- Biblioteca externa (Preline UI)
- JavaScript complexo
- Difícil customizar
- Conflitos com Vue

### DURANTE (Composable em cada componente)
- Código duplicado
- Imports em todo lugar
- Difícil manter

### DEPOIS (Plugin Global)
- Uma inicialização global
- Event delegation
- Fácil manter
- Performance otimizada
- Padrão Vue.js
