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
        <div>
            <folder-summary :summary="totals" />
            <collection-show-data-grid
                :data="list"
                :collection="collection"
                :search-terms="search"
            />
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/inertia-vue3";
import UiButton from "@/UI/UIButton";
import FolderSummary from "@/Components/CardLists/FolderSummary";
import CollectionShowDataGrid from "@/Pages/Collections/Partials/CollectionShowDataGrid";

export default {
    name: "Show",

    components: {
        Link,
        AppLayout,
        UiButton,
        FolderSummary,
        CollectionShowDataGrid,
    },

    props: {
        collection: {
            type: Object,
            default: () => {},
        },
        totals: {
            type: Object,
            default: () => {},
        },
        list: {
            type: Object,
            default: () => {},
        },
        search: {
            type: Object,
            default: () => {},
        },
    },
};
</script>
