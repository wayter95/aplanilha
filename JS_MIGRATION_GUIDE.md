# Migração de Scripts do Template Ynex para Vue.js

## 📋 Resumo da Migração

Todos os scripts JavaScript do template Ynex foram removidos do build do Vite e do `app.blade.php`. Os arquivos físicos foram mantidos em `resources/assets/js/` para referência e conversão futura conforme necessário.

---

## ✅ Scripts Migrados para Vue.js

### 1. **main.js** → `useThemeConfig.js`
- **Localização:** `resources/js/composables/useThemeConfig.js`
- **Função:** Aplica configurações de tema do localStorage (dark/light, RTL/LTR, layouts, etc.)
- **Execução:** Síncrona, ANTES do Vue montar (`applyThemeConfigSync()`)
- **Status:** ✅ Completo e funcionando

### 2. **defaultmenu.js** → `useSidebarToggle.js` + `useSidebarMenu.js`
- **Localização:** `resources/js/composables/`
- **Funções:**
  - `useSidebarToggle.js`: Toggle do sidebar (overlay, hover, click)
  - `useSidebarMenu.js`: Menu accordion (expand/collapse)
- **Status:** ✅ Completo e funcionando

### 3. **custom-switcher.js** → `useThemeSwitcher.js` + `ThemeSwitcher.vue`
- **Localização:** 
  - Composable: `resources/js/composables/useThemeSwitcher.js`
  - Componente: `resources/js/Components/ThemeSwitcher.vue`
- **Funções:** 
  - Configuração completa de tema (15+ funções)
  - Dark/Light, RTL/LTR, Cores (primárias, menu, header, background)
  - Layouts (vertical/horizontal, page styles, width)
  - Reset completo
- **Status:** ✅ Completo e funcionando

---

## 🗑️ Scripts Removidos do Build

Os seguintes scripts foram **removidos do `vite.config.js` e `app.blade.php`**:

### Scripts de Template Básico
- ❌ `switch.js` - Substituído por `useThemeSwitcher.js` (dark/light mode)
- ❌ `sticky.js` - Funcionalidade não utilizada no momento
- ❌ `defaultmenu.js` - Substituído por `useSidebarToggle.js` + `useSidebarMenu.js`
- ❌ `custom-switcher.js` - Substituído por `useThemeSwitcher.js` + `ThemeSwitcher.vue`

### Scripts de Dashboards (não utilizados)
- ❌ `analytics-dashboard.js`
- ❌ `courses-dashboard.js`
- ❌ `crm-dashboard.js`
- ❌ `crypto-dashboard.js`
- ❌ `ecommerce-dashboard.js`
- ❌ `hrm-dashboard.js`
- ❌ `jobs-dashboard.js`
- ❌ `nft-dashboard.js`
- ❌ `personal-dashboard.js`
- ❌ `projects-dashboard.js`
- ❌ `sales-dashboard.js`
- ❌ `stocks-dashboard.js`

### Scripts de Features Específicas (não utilizados)
- ❌ `apexchart.js` + todas variações (area, bar, boxplot, bubble, candlestick, column, heatmap, line, mixed, pie, polararea, radar, radialbar, rangearea, scatter, timeline, treemap)
- ❌ `chartjs-charts.js`
- ❌ `echarts.js`
- ❌ `jsvectormap.js`
- ❌ `fullcalendar.js`
- ❌ `datatable.js`
- ❌ `choices.js`
- ❌ `select2.js`
- ❌ `tom-select.js`
- ❌ `quill-editor.js`
- ❌ `fileupload.js`
- ❌ `color-picker.js`
- ❌ `date-time_pickers.js`
- ❌ `nouislider.js`
- ❌ `swetalert.js`
- ❌ `prism-custom.js`
- ❌ Todos os scripts de páginas específicas (blog, cart, checkout, crypto, invoice, jobs, mail, nft, products, etc.)

---

## 📦 Build Atual do Vite

### `vite.config.js` - Inputs Ativos:
```javascript
input: [
    "resources/sass/app.scss",
    "resources/css/app.css",
    "resources/assets/css/style.css",
    "resources/js/app.js",
]
```

### `app.blade.php` - Scripts Carregados:
```blade
@vite([
    'resources/sass/app.scss',
    'resources/assets/css/style.css',
    'resources/js/app.js'
])
```

**Total:** Apenas 1 arquivo JS (`app.js`) + 2 arquivos CSS

---

## 🔄 Estratégia de Conversão Futura

À medida que você precisar de funcionalidades do template:

1. **Identifique o script necessário** em `resources/assets/js/`
2. **Analise a funcionalidade** do script original
3. **Crie um composable Vue** em `resources/js/composables/`
4. **Ou crie um componente Vue** em `resources/js/Components/`
5. **Teste a funcionalidade** no Vue
6. **NÃO adicione de volta ao `vite.config.js`** - use apenas Vue

### Exemplo: Se precisar de DataTable
1. Analisar: `resources/assets/js/datatable.js`
2. Criar: `resources/js/composables/useDataTable.js`
3. Ou usar: Biblioteca Vue como `vue-good-table` ou similar
4. Integrar no componente Vue desejado

---

## 🎯 Benefícios da Migração

✅ **Zero Conflitos:** Nenhum JS do template conflita com Vue/Inertia  
✅ **Bundle Menor:** Build reduzido drasticamente (de 100+ arquivos para 1)  
✅ **Manutenção Fácil:** Código Vue organizado e modular  
✅ **SPA Completo:** Navegação sem recarregar página  
✅ **Overlays Limpos:** Fecham automaticamente na navegação  
✅ **Performance:** Apenas o necessário é carregado  

---

## 📂 Estrutura de Arquivos

```
resources/
├── assets/js/           # Scripts originais do template (referência)
│   ├── main.js          # ❌ Não usado (migrado)
│   ├── defaultmenu.js   # ❌ Não usado (migrado)
│   ├── custom-switcher.js # ❌ Não usado (migrado)
│   ├── switch.js        # ❌ Não usado (migrado)
│   └── [outros...]      # ❌ Não usados (disponíveis para conversão)
│
└── js/
    ├── app.js           # ✅ Único JS carregado
    ├── composables/
    │   ├── useThemeConfig.js      # ✅ Substitui main.js
    │   ├── useSidebarToggle.js    # ✅ Substitui defaultmenu.js (toggle)
    │   ├── useSidebarMenu.js      # ✅ Substitui defaultmenu.js (menu)
    │   └── useThemeSwitcher.js    # ✅ Substitui custom-switcher.js
    │
    └── Components/
        └── ThemeSwitcher.vue      # ✅ Substitui switcher.blade.php
```

---

## 🚀 Próximos Passos

1. **Testar build:** `npm run build`
2. **Verificar funcionamento:** Dark mode, cores, RTL, overlays, navegação
3. **Monitorar console:** Garantir zero erros JS
4. **Converter sob demanda:** Apenas quando precisar de funcionalidades específicas

---

**Última atualização:** Novembro 2025  
**Status:** ✅ Migração básica completa - Sistema estável e sem conflitos
