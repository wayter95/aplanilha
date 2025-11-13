/**
 * Preline UI Plugin
 * 
 * Centraliza inicialização de componentes Preline
 * - HSDropdown
 * - HSOverlay
 * - HSTooltip
 * - HSAccordion
 * - HSTabs
 * - HSCollapse
 * - etc
 * 
 * Uso:
 * - initPreline() no app.js (mount inicial)
 * - reinitPreline() após navegação Inertia
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
 */
const OPTIONAL_COMPONENTS = {
    HSAccordion,
    // HSTabs - REMOVIDO! Será inicializado manualmente nos componentes que usam
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
            console.log(`[Preline] ${name} skipped (no elements found)`);
            return;
        }
        
        try {
            Component.autoInit();
            console.log(`[Preline] ${name} initialized`);
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
        // ESTRATÉGIA 1: Tentar usar HSStaticMethods (mais confiável)
        try {
            if (HSStaticMethods && HSStaticMethods.autoInit) {
                HSStaticMethods.autoInit();
                console.log('[Preline] All components reinitialized via HSStaticMethods');
                return;
            }
        } catch (error) {
            console.log('[Preline] HSStaticMethods not available, using safe reinit');
        }
        
        // ESTRATÉGIA 2: Reinicializar componentes ESSENCIAIS sempre
        Object.entries(ESSENTIAL_COMPONENTS).forEach(([name, Component]) => {
            if (!Component || !Component.autoInit) return;
            
            try {
                Component.autoInit();
                console.log(`[Preline] ${name} reinitialized (essential)`);
            } catch (error) {
                console.error(`[Preline] Error reinitializing ${name}:`, error);
            }
        });
        
        // ESTRATÉGIA 3: Reinicializar componentes OPCIONAIS apenas se existirem
        Object.entries(OPTIONAL_COMPONENTS).forEach(([name, Component]) => {
            if (!Component || !Component.autoInit) return;
            
            // Verificar se existem elementos ANTES de tentar inicializar
            if (!hasElementsForComponent(name)) {
                // Não logar para não poluir console
                return;
            }
            
            try {
                Component.autoInit();
                console.log(`[Preline] ${name} reinitialized (optional)`);
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
 */
export function initTabs() {
    if (!HSTabs) {
        console.warn('[Preline] HSTabs not available');
        return;
    }
    
    try {
        const tabElements = document.querySelectorAll('[data-hs-tab]');
        if (tabElements.length > 0) {
            HSTabs.autoInit();
            console.log(`[Preline] HSTabs initialized manually (${tabElements.length} tabs found)`);
        }
    } catch (error) {
        console.warn('[Preline] HSTabs init failed (safe to ignore):', error.message);
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
