import './bootstrap';
import '../css/app.css';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';

const pages = import.meta.glob('./Pages/**/*.vue');

createInertiaApp({
    title: title => (title ? `${title} · Astrologer Dashboard` : 'Astrologer Dashboard'),
    resolve: name => pages[`./Pages/${name}.vue`]().then(module => module.default),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});

InertiaProgress.init({ color: '#6366f9' });
