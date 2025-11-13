/**
 * Composable: useTabs
 * 
 * Inicializa HSTabs do Preline em componentes específicos
 * 
 * USO:
 * import { useTabs } from '@/composables/useTabs'
 * 
 * // No componente Vue:
 * onMounted(() => {
 *   useTabs()
 * })
 */

import { onMounted, onUnmounted } from 'vue';
import { initTabs } from '@/plugins/preline';

export function useTabs() {
    let tabsInitialized = false;

    onMounted(() => {
        // Pequeno delay para garantir que DOM foi renderizado
        setTimeout(() => {
            initTabs();
            tabsInitialized = true;
        }, 50);
    });

    onUnmounted(() => {
        // Cleanup se necessário
        tabsInitialized = false;
    });

    return {
        initialized: tabsInitialized
    };
}
