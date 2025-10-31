import { createInertiaApp } from "@inertiajs/vue3";
import { createApp, h } from "vue";
import { createPinia } from 'pinia'
import "../assets/css/icons.css";
import "../assets/css/style.css";
import "../css/app.css";
import "../sass/app.scss";
import "./bootstrap.js";
import "./plugins/validation.js";

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
