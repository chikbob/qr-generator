import './bootstrap'
import '../css/app.css'

import {createApp, h} from 'vue'
import {createInertiaApp} from '@inertiajs/vue3'
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers'
import {ZiggyVue} from '../../vendor/tightenco/ziggy'

import { useI18n } from '@/Lang/useI18n'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        ),
    setup({el, App, props, plugin}) {
        const vueApp = createApp({render: () => h(App, props)})

        const i18n = useI18n()

        // 🌍 глобально
        vueApp.config.globalProperties.$t = i18n.t
        vueApp.provide('i18n', i18n)

        return vueApp
            .use(plugin)
            .use(ZiggyVue)
            .mount(el)
    },
    progress: {
        color: '#4B5563',
    },
})
