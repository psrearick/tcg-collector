<template>
    <app-layout title="User">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight py-2">
                {{ collection.name }}
            </h2>
            <p class="text-sm text-gray-500">
                {{ collection.description }}
            </p>
        </div>
        <div>
            <folder-summary v-if="loaded" :summary="totals" class="pt-6" />
            <collections-data-grid
                :collection="collection"
                :table="table"
                :search-url="searchUrl"
                :is-group="true"
                @searched="updateData"
            />
        </div>
    </app-layout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import FolderSummary from "@/Components/CardLists/FolderSummary";
import CollectionsDataGrid from "@/Pages/Collections/Partials/CollectionsDataGrid";
import CollectionsShowTableMixin from "@/Pages/Collections/Mixins/CollectionsShowTableMixin";

export default {
    name: "Show",

    components: {
        AppLayout,
        FolderSummary,
        CollectionsDataGrid,
    },

    mixins: [CollectionsShowTableMixin],

    props: {
        collection: {
            type: Object,
            default: () => {},
        },
        groupUser: {
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
