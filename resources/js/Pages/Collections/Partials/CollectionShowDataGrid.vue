<template>
    <div>
        <card-set-search
            v-model="cardSearchTerm"
            v-model:set-name="setSearchTerm"
            v-model:searching="searching"
            :mark-searching="true"
            :configure-table="true"
        />
        <ui-data-table
            v-if="showData"
            class="mt-4"
            :data="data.data"
            :fields="table.fields"
            :grid-name="gridName"
        />
        <ui-data-grid-pagination-no-link
            :pagination="paginator"
            @update:pagination="updatePagination"
        />
        <ui-grid-configuration-panel
            v-model:show="gridConfigurationPanelShow"
            :fields="table.fields"
            :grid-name="gridName"
        />
    </div>
</template>

<script>
import UiDataTable from "@/UI/DataGrid/UIDataTable";
import UiDataGridPaginationNoLink from "@/UI/DataGrid/UIDataGridPaginationNoLink";
import CardSetSearch from "@/Pages/Cards/Partials/CardSetSearch";
import UiGridConfigurationPanel from "@/UI/DataGrid/UIGridConfigurationPanel";
import CollectionsShowTable from "@/Pages/Collections/Mixins/CollectionsShowTable";

export default {
    name: "CollectionShowDataGrid",

    components: {
        CardSetSearch,
        UiDataTable,
        UiDataGridPaginationNoLink,
        UiGridConfigurationPanel,
    },

    mixins: [CollectionsShowTable],

    props: {
        data: {
            type: Object,
            default: () => {},
        },
        searchTerms: {
            type: Object,
            default: () => {},
        },
        collection: {
            type: Object,
            default: () => {},
        },
    },

    data() {
        return {
            tableData: {},
            cardSearchTerm: "",
            setSearchTerm: "",
            gridConfigurationPanelShow: false,
            searching: false,
        };
    },

    computed: {
        showData() {
            if (typeof this.data === "undefined") {
                return false;
            }
            if (!this.data) {
                return false;
            }
            return true;
        },
    },

    watch: {
        cardSearchTerm() {
            if (!this.searching) {
                return;
            }
            this.paginator = this.default_paginator;
            this.search();
        },
        setSearchTerm() {
            if (!this.searching) {
                return;
            }
            this.paginator = this.default_paginator;
            this.search();
        },
    },

    mounted() {
        this.paginator = _.pick(this.data, [
            "current_page",
            "from",
            "last_page",
            "per_page",
            "to",
            "total",
            "links",
        ]);
        this.cardSearchTerm = this.searchTerms.card;
        this.setSearchTerm = this.searchTerms.set;
    },

    methods: {
        updatePagination(pagination) {
            this.paginator = pagination;
            this.search();
        },
        search: _.debounce(function () {
            this.$inertia.get(
                "/collections/" + this.collection.uuid,
                {
                    card: this.cardSearchTerm,
                    set: this.setSearchTerm,
                    paginator: this.paginator,
                    sort: this.sortFields,
                    sortOrder: this.sortOrder,
                    filters: this.filters,
                },
                {
                    preserveState: true,
                    onSuccess: () => {
                        this.searching = false;
                    },
                }
            );
        }, 1200),
    },
};
</script>
