/**
 * Preline UI Plugin
 * 
 * Centraliza inicialização de componentes Preline
 * - HSDropdown
 * - HSOverlay
 * - HSTooltip
 * - HSAccordion
 * - HSTabs (MANUAL - não auto-init)
 * - HSCollapse
 * - etc
 * 
 * Uso:
 * - initPreline() no app.js (mount inicial)
 * - reinitPreline() após navegação Inertia
 * 
 * IMPORTANTE: HSTabs NÃO é inicializado automaticamente!
 * Use initTabs() manualmente em componentes que têm tabs.
 */

import { 
    HSDropdown, 
    HSOverlay, 
    HSTooltip, 
    HSAccordion,
    HSTabs,
    HSCollapse,
    HSStaticMethods
} from 'preline';

// Desabilitar auto-init do HSTabs para evitar erros
// HSTabs será inicializado manualmente apenas quando necessário
if (typeof window !== 'undefined' && HSTabs) {
    // Sobrescrever o autoInit do HSTabs para torná-lo seguro
    const originalAutoInit = HSTabs.autoInit;
    HSTabs.autoInit = function() {
        try {
            // Só executar se houver elementos [data-hs-tab]
            const tabElements = document.querySelectorAll('[data-hs-tab]');
            if (tabElements.length > 0) {
                return originalAutoInit.call(this);
            }
        } catch (error) {
            // Silenciar erros do HSTabs
            return;
        }
    };
}

/**
 * Componentes Preline ESSENCIAIS (sempre inicializar)
 * Estes existem em quase todas as páginas
 */
const ESSENTIAL_COMPONENTS = {
    HSDropdown,  // Header dropdown sempre existe
    HSOverlay,   // Modals e sidebars
    HSTooltip    // Tooltips globais
};

/**
 * Componentes Preline OPCIONAIS (inicializar se existirem elementos)
 * Estes só existem em páginas específicas
 * 
 * NOTA: HSTabs foi REMOVIDO daqui porque causa erros quando não há tabs
 * Use initTabs() manualmente nos componentes que precisam de tabs
 */
const OPTIONAL_COMPONENTS = {
    HSAccordion,
    HSCollapse
};

/**
 * Componentes Preline para auto-init (todos)
 */
const PRELINE_COMPONENTS = {
    ...ESSENTIAL_COMPONENTS,
    ...OPTIONAL_COMPONENTS
};

/**
 * Verificar se existem elementos para inicializar
 */
function hasElementsForComponent(name) {
    const selectors = {
        HSDropdown: '[data-hs-dropdown]',
        HSOverlay: '[data-hs-overlay]',
        HSTooltip: '[data-hs-tooltip]',
        HSAccordion: '[data-hs-accordion]',
        HSTabs: '[data-hs-tab]',
        HSCollapse: '[data-hs-collapse]'
    };
    
    const selector = selectors[name];
    if (!selector) return true; // Se não conhecemos o seletor, tentar inicializar
    
    try {
        const elements = document.querySelectorAll(selector);
        return elements && elements.length > 0;
    } catch (e) {
        console.warn(`[Preline] Error checking elements for ${name}:`, e);
        return false; // Não inicializar se houver erro
    }
}

/**
 * Inicializar Preline (primeira vez)
 * Chamar no mount do app
 */
export function initPreline() {
    Object.entries(PRELINE_COMPONENTS).forEach(([name, Component]) => {
        if (!Component || !Component.autoInit) return;
        
        // Verificar se existem elementos antes de inicializar
        if (!hasElementsForComponent(name)) {
            // Silencioso para componentes opcionais
            if (OPTIONAL_COMPONENTS[name]) return;
            return;
        }
        
        try {
            Component.autoInit();
        } catch (error) {
            console.error(`[Preline] Error initializing ${name}:`, error);
        }
    });
}

/**
 * Reinicializar Preline
 * Chamar após navegação Inertia (SPA)
 */
export function reinitPreline() {
    // Pequeno delay para garantir que DOM foi atualizado
    setTimeout(() => {
        // NÃO usar HSStaticMethods.autoInit() porque ele inicializa HSTabs
        // que causa erro quando não há tabs na página
        
        // ESTRATÉGIA 1: Reinicializar componentes ESSENCIAIS sempre
        Object.entries(ESSENTIAL_COMPONENTS).forEach(([name, Component]) => {
            if (!Component || !Component.autoInit) return;
            
            try {
                Component.autoInit();
            } catch (error) {
                console.error(`[Preline] Error reinitializing ${name}:`, error);
            }
        });
        
        // ESTRATÉGIA 2: Reinicializar componentes OPCIONAIS apenas se existirem
        Object.entries(OPTIONAL_COMPONENTS).forEach(([name, Component]) => {
            if (!Component || !Component.autoInit) return;
            
            // Verificar se existem elementos ANTES de tentar inicializar
            if (!hasElementsForComponent(name)) {
                // Não logar para não poluir console
                return;
            }
            
            try {
                Component.autoInit();
            } catch (error) {
                // Erro silencioso para componentes opcionais
                console.warn(`[Preline] ${name} reinit failed (optional, safe to ignore)`, error.message);
            }
        });
    }, 100);
}

/**
 * Destruir instâncias Preline (se necessário)
 * Útil para evitar memory leaks
 */
export function destroyPreline() {
    // Preline não tem método destroy nativo, mas podemos limpar manualmente
    // se necessário no futuro
}

/**
 * Inicializar HSTabs manualmente (para uso em componentes específicos)
 * IMPORTANTE: Só chame esta função em componentes que realmente têm tabs!
 */
export function initTabs() {
    if (!HSTabs) {
        console.warn('[Preline] HSTabs not available');
        return;
    }
    
    try {
        const tabElements = document.querySelectorAll('[data-hs-tab]');
        if (tabElements.length === 0) {
            // Não há tabs na página, não fazer nada
            return;
        }
        
        // Verificar se já foram inicializados para evitar duplicação
        const alreadyInitialized = Array.from(tabElements).some(el => {
            return el.classList.contains('hs-tab-active') || el.hasAttribute('data-hs-tab-initialized');
        });
        
        if (!alreadyInitialized) {
            HSTabs.autoInit();
        }
    } catch (error) {
        // Silenciar erro se não for crítico
        if (!error.message.includes("can't access property")) {
            console.warn('[Preline] HSTabs init failed:', error.message);
        }
    }
}

/**
 * Export components para uso direto (se necessário)
 */
export { 
    HSDropdown, 
    HSOverlay, 
    HSTooltip, 
    HSAccordion, 
    HSTabs, 
    HSCollapse,
    HSStaticMethods 
};
