<template>
    <app-layout title="Collection">
        <template #header>
            <div class="flex flex-wrap justify-between">
                <div>
                    <h2
                        class="
                            font-semibold
                            text-xl text-gray-800
                            leading-tight
                            py-2
                        "
                    >
                        {{ collection.name }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        {{ collection.description }}
                    </p>
                </div>
                <div class="flex space-x-4">
                    <Link :href="route('folders.create')">
                        <ui-button text="Import" button-style="primary-dark" />
                    </Link>

                    <Link :href="route('collections.edit', collection.uuid)">
                        <ui-button text="Edit" button-style="primary-dark" />
                    </Link>

                    <Link
                        :href="
                            collection.folder_uuid
                                ? route('folders.show', collection.folder_uuid)
                                : route('collections.index')
                        "
                    >
                        <ui-button
                            text="Back to Folder"
                            button-style="primary-dark"
                        />
                    </Link>
                </div>
            </div>
        </template>
        Collection Detail
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/inertia-vue3";
import UiButton from "@/UI/UIButton";

export default {
    name: "Show",

    components: { Link, AppLayout, UiButton },

    props: {
        collection: {
            type: Object,
            default: () => {},
        },
    },
};
</script>
