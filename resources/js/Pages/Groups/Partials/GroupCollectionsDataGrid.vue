<template>
    <div>
        <div v-if="collectionsComputed">
            <ui-data-table
                class="mt-4"
                :data="collectionsComputed"
                :fields="table.fields"
                :grid-name="table.gridName"
            />
            <ui-data-grid-pagination-no-link
                :pagination="paginator"
                @update:pagination="updatePagination"
            />
        </div>
    </div>
</template>
<script>
import UiDataTable from "@/UI/DataGrid/UIDataTable";
import UiDataGridPaginationNoLink from "@/UI/DataGrid/UIDataGridPaginationNoLink";

export default {
    name: "GroupCollectionsDataGrid",

    components: { UiDataTable, UiDataGridPaginationNoLink },

    props: {
        collections: {
            type: Object,
            default: () => {},
        },
        userId: {
            type: Number,
            default: null,
        },
    },

    data() {
        return {
            collectionList: [],
            paginator: {},
            table: {
                gridName: "group_collections",
                fields: [
                    {
                        visible: true,
                        sortable: true,
                        type: "text",
                        link: true,
                        key: "name",
                        label: "Collection",
                        events: {
                            click: "group_collection_name_click",
                        },
                    },
                    {
                        visible: true,
                        sortable: false,
                        type: "text",
                        label: "Card Count",
                        key: "total_cards",
                    },
                    {
                        visible: true,
                        sortable: true,
                        type: "currency",
                        label: "Price",
                        key: "current_value",
                    },
                    {
                        visible: true,
                        sortable: true,
                        type: "text",
                        label: "Owner",
                        key: "user_name",
                    },
                ],
            },
        };
    },

    computed: {
        collectionsComputed() {
            return this.collectionList.map((collection) => {
                collection.total_cards = collection.summary_data.total_cards;
                collection.current_value =
                    collection.summary_data.current_value;
                collection.user_name = collection.user.name;
                return collection;
            });
        },
    },

    watch: {
        userId(val) {
            const collectionsClone = _.cloneDeep(this.collections.data);
            if (!val) {
                this.collectionList = collectionsClone;
            } else {
                this.collectionList = collectionsClone.filter(
                    (collection) => collection.user_id === val
                );
            }
        },
    },

    mounted() {
        this.collectionList = _.cloneDeep(this.collections.data);
        this.paginator = _.pick(this.collections, [
            "current_page",
            "from",
            "last_page",
            "per_page",
            "to",
            "total",
            "links",
        ]);
    },

    methods: {
        updatePagination(pagination) {
            this.paginator = pagination;
            this.search();
        },
        search() {
            //
        },
    },
};
</script>
