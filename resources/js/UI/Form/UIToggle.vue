<template>
    <div class="flex">
        <button
            type="button"
            :class="[
                enabled ? 'bg-primary-600' : 'bg-gray-200',
                'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none',
            ]"
            role="switch"
            aria-checked="false"
            @click="updateEnabled(!enabled)"
        >
            <span class="sr-only">Use setting</span>
            <span
                aria-hidden="true"
                :class="[
                    enabled ? 'translate-x-5' : 'translate-x-0',
                    'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200',
                ]"
            />
        </button>
        <span class="ml-2 text-sm text-gray-600 py-1">{{ label }}</span>
        <slot />
    </div>
</template>

<script>
export default {
    name: "UiToggle",

    props: {
        enabled: {
            type: Boolean,
            default: false,
        },
        label: {
            type: String,
            default: "",
        },
    },

    emits: ["update:enabled"],

    methods: {
        updateEnabled(value) {
            this.$emit("update:enabled", value);
        },
    },
};
</script>
