<template>
    <div>
        <card-set-search
            v-model="cardSearchTerm"
            v-model:set-name="setSearchTerm"
            v-model:searching="searching"
            :mark-searching="true"
            :configure-table="true"
            @grid-configuration-click="gridConfigurationPanelShow = true"
        />
        <div v-if="loaded && notEmpty">
            <ui-data-table
                v-if="showData"
                class="mt-4"
                :data="data.data"
                :fields="table.fields"
                :field-rows="table.fieldRows"
                :grid-name="table.gridName"
                :select-menu="table.selectMenu"
                :has-expand-toggle="$settings.hasSettings()"
                :expanded-default="expandedDefault"
                @expand="expand"
                @expandRow="expandRow"
            />
            <ui-data-grid-pagination-no-link
                :pagination="paginator"
                @update:pagination="updatePagination"
            />
        </div>
        <div class="mt-4">
            <ui-well v-if="loaded && !notEmpty">
                This Collection is Empty
            </ui-well>
            <ui-well v-if="!loaded">Loading...</ui-well>
        </div>
        <ui-grid-configuration-panel
            v-model:show="gridConfigurationPanelShow"
            :fields="table.fields"
            :grid-name="table.gridName"
        />
        <move-to-collection-panel
            v-model:show="moveToCollectionPanelShow"
            :data="moveToCollectionPanelData"
            :collection="collection"
            @saved="itemMoved"
            @close="clearPanelData"
        />
        <remove-from-collection-panel
            v-model:show="removeFromCollectionPanelShow"
            :data="removeFromCollectionPanelData"
            :collection="collection"
            @saved="itemRemoved"
        />
    </div>
</template>

<script>
import UiDataTable from "@/UI/DataGrid/UIDataTable";
import UiDataGridPaginationNoLink from "@/UI/DataGrid/UIDataGridPaginationNoLink";
import CardSetSearch from "@/Pages/Cards/Partials/CardSetSearch";
import UiGridConfigurationPanel from "@/UI/DataGrid/UIGridConfigurationPanel";
import MoveToCollectionPanel from "@/Components/Panels/MoveToCollectionPanel";
import RemoveFromCollectionPanel from "@/Components/Panels/RemoveFromCollectionPanel";
import CollectionsTableMixin from "@/Pages/Collections/Mixins/CollectionsTableMixin";
import UiWell from "@/UI/UIWell";

export default {
    name: "CollectionsDataGrid",

    components: {
        CardSetSearch,
        MoveToCollectionPanel,
        RemoveFromCollectionPanel,
        UiDataTable,
        UiDataGridPaginationNoLink,
        UiGridConfigurationPanel,
        UiWell,
    },

    mixins: [CollectionsTableMixin],

    props: {
        collection: {
            type: Object,
            default: () => {},
        },
        table: {
            type: Object,
            default: () => {},
        },
        searchUrl: {
            type: String,
            default: "",
        },
        isGroup: {
            type: Boolean,
            default: false,
        },
    },

    emits: ["searched"],

    data() {
        return {
            tableData: {},
            cardSearchTerm: "",
            setSearchTerm: "",
            gridConfigurationPanelShow: false,
            searching: false,
            data: {
                data: [],
                paginator: {},
            },
            searchData: {},
            loaded: false,
            removeFromCollectionPanelShow: false,
            removeFromCollectionPanelData: {},
            moveToCollectionPanelShow: false,
            moveToCollectionPanelData: {},
            expandedDefault: false,
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
        notEmpty() {
            return this.data.data.length > 0;
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
        this.search();
        let expanded = this.$settings.expandedDefault("show");
        if (this.table.gridName === "collection-edit") {
            expanded = this.$settings.expandedDefault("edit");
        }
        this.expandedDefault = expanded || false;
    },

    methods: {
        clearDataGrid() {
            this.emitter.emit(
                "clear_data_grid_selections",
                this.table.gridName
            );
        },
        clearPanelData() {
            this.moveToCollectionPanelData = {};
        },
        expand(expand) {
            this.data.data.forEach((item, key) => {
                this.expandRow({
                    expanded: expand,
                    key: key,
                });
            });
        },
        expandRow(expand) {
            this.emitter.emit("expandBottomRow", {
                expand: expand.expanded,
                field: this.table.fields[expand.key],
                data: this.data.data[expand.key],
            });
        },
        itemMoved() {
            this.clearDataGrid();
            this.search();
        },
        itemRemoved() {
            this.clearDataGrid();
            this.search();
        },
        updatePagination(pagination) {
            this.paginator = pagination;
            this.search();
        },
        processData(res) {
            let list = res.list;
            if (this.$settings.hasSettings()) {
                let masters = [];
                list.data.forEach((cardGroup) => {
                    Object.values(cardGroup).forEach((finishGroup) => {
                        let master = _.cloneDeep(finishGroup[0]);
                        master.cards = _.cloneDeep(finishGroup);
                        master.quantity = finishGroup
                            .map((card) => card.quantity)
                            .reduce((prev, cur) => prev + cur);
                        master.cards = master.cards.map((card) => {
                            card.collection_uuid = this.collection.uuid;
                            return card;
                        });
                        master.showRow = this.expandedDefault;
                        masters.push(master);
                    });
                });

                list.data = masters;
            }
            this.data = list;
            this.searchData = res.search;
            this.paginator = _.pick(res.list, [
                "current_page",
                "from",
                "last_page",
                "per_page",
                "to",
                "total",
                "links",
            ]);
            this.cardSearchTerm = this.searchData.card;
            this.setSearchTerm = this.searchData.set;
        },
        search: _.debounce(function () {
            axios
                .post(this.searchUrl, {
                    card: this.cardSearchTerm,
                    set: this.setSearchTerm,
                    paginator: this.paginator,
                    sort: this.sortFields,
                    sortOrder: this.sortOrder,
                    filters: this.filters,
                    inGroup: this.isGroup,
                })
                .then((res) => {
                    this.searching = false;
                    this.loaded = true;
                    this.processData(res.data);
                    this.$emit("searched", res.data);
                });
        }, 1200),
    },
};
</script>
