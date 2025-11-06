import { createInertiaApp } from "@inertiajs/vue3";
import { createApp, h } from "vue";
import { createPinia } from 'pinia'
import { router } from '@inertiajs/vue3'
import "./bootstrap.js";
import "./plugins/validation.js";

// Import Popper.js globally for template scripts
import * as Popper from '@popperjs/core';
window.Popper = Popper;

// Import Preline for dropdowns and UI components
import('preline/preline').then(() => {
    // Reinicializar Preline após cada navegação do Inertia
    router.on('navigate', () => {
        setTimeout(() => {
            if (window.HSStaticMethods) {
                window.HSStaticMethods.autoInit();
            }
        }, 100);
    });
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
