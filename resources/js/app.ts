import '../css/app.css';

import AppLayout from '@/layout/AppLayout.vue';
import { createInertiaApp, Head, Link } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import 'vue-sonner/style.css';

createInertiaApp({
    title: (title) => {
        const appName = import.meta.env.VITE_APP_NAME || 'Coffee Riders';
        return title ? `${title} | ${appName}` : appName;
    },
    resolve: async (name) => {
        const pages = import.meta.glob('./pages/**/*.vue');

        const module: any = await resolvePageComponent(`./pages/${name}.vue`, pages);
        const component = module.default || module;
        return Object.assign({}, component, {
            layout: component.layout ?? AppLayout,
        });
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component('Head', Head)
            .component('Link', Link)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
// initializeTheme();
