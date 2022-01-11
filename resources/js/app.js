require("./bootstrap");

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/inertia-vue3";
import { InertiaProgress } from "@inertiajs/progress";
import currency from "currency.js";

import { closable } from "@/directives";

import settings from "./Shared/api/Settings";
import * as convertValue from "./Shared/api/ConvertValue";
import store from "./Store";
import mitt from "mitt";
const emitter = mitt();

const appName =
    window.document.getElementsByTagName("title")[0]?.innerText || "Laravel";

export const app = createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {
        const vueApp = createApp({ render: () => h(app, props) })
            .use(plugin)
            .mixin({ methods: { route } })
            .directive("closable", closable);
        vueApp.use(store);
        vueApp.use(settings);
        vueApp.config.globalProperties.$convertValue = convertValue;
        vueApp.config.globalProperties.currency = currency;
        vueApp.config.globalProperties.emitter = emitter;
        vueApp.mount(el);
        return vueApp;
    },
});

InertiaProgress.init({ color: "#4B5563" });
