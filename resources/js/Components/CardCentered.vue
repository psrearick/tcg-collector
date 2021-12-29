<template>
    <div
        class="
            min-h-screen
            bg-gray-200
            flex flex-col
            justify-center
            py-12
            sm:px-6
            lg:px-8
        "
    >
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center">
                <slot name="logo" />
            </div>
            <h2
                v-if="title"
                class="mt-6 text-center text-3xl font-extrabold text-gray-900"
                v-html="title"
            />
            <p
                v-if="subtitle"
                class="mt-2 text-center text-sm text-gray-600"
                v-html="subtitle"
            ></p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <ui-card>
                <slot />
            </ui-card>
        </div>
    </div>
</template>

<script>
import UiCard from "@/UI/UICard";
export default {
    name: "CardCentered",
    components: { UiCard },
    props: {
        title: {
            type: String,
            default: null,
        },
        subtitle: {
            type: String,
            default: null,
        },
    },
};
</script>
