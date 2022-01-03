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
            <folder-summary v-if="loaded" :summary="totals" class="pt-6" />
            <collections-data-grid
                :collection="collection"
                :table="table"
                :search-url="searchUrl"
                @searched="updateData"
            />
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/inertia-vue3";
import UiButton from "@/UI/UIButton";
import FolderSummary from "@/Components/CardLists/FolderSummary";
import CollectionsDataGrid from "@/Pages/Collections/Partials/CollectionsDataGrid";
import CollectionsShowTableMixin from "@/Pages/Collections/Mixins/CollectionsShowTableMixin";

export default {
    name: "Show",

    components: {
        Link,
        AppLayout,
        UiButton,
        FolderSummary,
        CollectionsDataGrid,
    },

    mixins: [CollectionsShowTableMixin],

    props: {
        collection: {
            type: Object,
            default: () => {},
        },
    },

    data() {
        return {
            totals: {},
            loaded: false,
        };
    },

    methods: {
        updateData(data) {
            this.totals = data.totals;
            this.loaded = true;
        },
    },
};
</script>
