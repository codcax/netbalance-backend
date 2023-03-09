import { createApp, h, type DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import ElementPlus from 'element-plus';
import 'element-plus/dist/index.css';

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob<Promise<DefineComponent>>('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ElementPlus)
            .mount(el);
    },
});
