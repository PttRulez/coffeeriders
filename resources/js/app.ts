import '../css/app.css';

import AppLayout from '@/layout/AppLayout.vue';
import { createInertiaApp, Head, Link } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';

// Extend ImportMeta interface for Vite...
declare module 'vite/client' {
    interface ImportMetaEnv {
        readonly VITE_APP_NAME: string;

        [key: string]: string | boolean | undefined;
    }

    interface ImportMeta {
        readonly env: ImportMetaEnv;
        readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>;
    }
}

createInertiaApp({
    title: (title) => {
        const appName = import.meta.env.VITE_APP_NAME || 'Coffee Riders';
        return title ? `${title} | ${appName}` : appName;
    },
    // resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    resolve: async (name) => {
        const pages = import.meta.glob('./pages/**/*.vue');
        const page = await resolvePageComponent(`./pages/${name}.vue`, pages);
        if (page.default.layout === undefined) {
            page.default.layout = AppLayout;
        }

        // if (name.toLowerCase().startsWith('auth/')) {
        //     page.default.layout = AuthLayout;
        // }

        return page;
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
initializeTheme();
