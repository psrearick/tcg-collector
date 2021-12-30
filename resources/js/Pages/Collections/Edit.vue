<template>
    <app-layout title="Edit Collection">
        <template #header>
            <div class="flex justify-between">
                <div>
                    <h2
                        class="
                            font-semibold
                            text-xl text-gray-800
                            leading-tight
                            py-2
                        "
                    >
                        Edit {{ collection.name }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        {{ collection.description }}
                    </p>
                </div>
                <div class="flex space-x-4">
                    <Link :href="route('collections.show', collection.uuid)">
                        <ui-button
                            text="Done Editing"
                            button-style="primary-dark"
                        />
                    </Link>
                </div>
            </div>
        </template>
    </app-layout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/inertia-vue3";
import UiButton from "@/UI/UIButton";

export default {
    name: "Edit",

    components: { AppLayout, Link, UiButton },

    props: {
        collection: {
            type: Object,
            default: () => {},
        },
    },
};
</script>
