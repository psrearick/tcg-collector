<template>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        <ui-card
            v-for="(option, index) in options"
            :key="index"
            :active="value === option.key"
            :mode="value === option.key ? 'primary' : 'default'"
            padding="p-4"
            class="cursor-pointer"
            card-style="white"
            @click="$emit('update:value', option.key)"
        >
            <div class="block flex flex-col">
                <span
                    :class="
                        'cursor-pointer block flex justify-between mb-2 h-6 text-sm font-medium ' +
                        (option.key === value
                            ? 'text-primary-800'
                            : 'text-gray-900')
                    "
                >
                    {{ option.label }}
                    <ui-icon
                        v-if="option.key === value"
                        icon="solid-circle-check"
                        class="text-primary-500"
                        size="1.5rem"
                    />
                </span>
                <span
                    class="block text-sm"
                    :class="
                        'cursor-pointer block text-sm ' +
                        (option.key === value
                            ? 'text-primary-700'
                            : 'text-gray-500')
                    "
                >
                    {{ option.description }}
                </span>
            </div>
        </ui-card>
    </div>
</template>

<script>
import UiCard from "@/UI/UICard";
import UiIcon from "@/UI/UIIcon";
export default {
    name: "UiRadioCardList",

    components: { UiIcon, UiCard },

    props: {
        options: {
            type: Array,
            default: () => {},
        },
        key: {
            type: String,
            default: "key",
        },
        value: {
            type: String,
            default: "",
        },
    },

    emits: ["update:value"],
};
</script>
