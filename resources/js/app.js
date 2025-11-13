import { createInertiaApp } from "@inertiajs/vue3";
import { createApp, h } from "vue";
import { createPinia } from 'pinia'
import { router } from '@inertiajs/vue3'
import "./bootstrap.js";
import "./plugins/validation.js";

// Aplicar configurações de tema ANTES de tudo (inclui sidebar state)
import { applyThemeConfigSync } from '@/composables/useThemeConfig'
applyThemeConfigSync()

// Inicializar e configurar loader
import { useLoader } from '@/composables/useLoader'
const { initLoader, setupInertiaListeners } = useLoader()
initLoader()
setupInertiaListeners()

// Import Popper.js globally for template scripts
import * as Popper from '@popperjs/core';
window.Popper = Popper;

// Inicializar Preline UI
import { initPreline, reinitPreline } from '@/plugins/preline'
initPreline()

// Reinicializar Preline após navegação Inertia
router.on('navigate', () => {
  reinitPreline()
});

const pinia = createPinia()

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
        app.use(plugin)
        app.use(pinia)
        app.mount(el);
    },
});


