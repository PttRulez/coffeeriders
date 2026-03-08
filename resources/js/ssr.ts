import AppLayout from '@/layout/AppLayout.vue';
import { createInertiaApp, Head, Link } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { renderToString } from '@vue/server-renderer';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createSSRApp, DefineComponent, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';

createServer(
    (page) =>
        createInertiaApp({
            page,
            render: renderToString,
            title: (title) => {
                const appName = import.meta.env.VITE_APP_NAME || 'Coffee Riders';
                return title ? `${title} | ${appName}` : appName;
            },
            resolve: resolvePage,
            setup: ({ App, props, plugin }) =>
                createSSRApp({ render: () => h(App, props) })
                    .use(plugin)
                    .use(ZiggyVue, {
                        ...page.props.ziggy,
                        location: new URL(page.props.ziggy.location),
                    })
                    .component('Head', Head)
                    .component('Link', Link),
        }),
    { cluster: true },
);

async function resolvePage(name: string) {
    const pages = import.meta.glob<DefineComponent>('./pages/**/*.vue');
    const module = await resolvePageComponent<DefineComponent>(`./pages/${name}.vue`, pages);
    const component = (module as any).default || module;

    component.layout = component.layout ?? AppLayout;
    return component;
}
