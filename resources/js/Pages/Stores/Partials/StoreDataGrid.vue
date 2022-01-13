<template>
    <div>
        <ui-data-table
            class="mt-4"
            :data="stores.data"
            :fields="table.fields"
            :grid-name="table.gridName"
        />
        <ui-data-grid-pagination-no-link
            :pagination="paginator"
            @update:pagination="updatePagination"
        />
    </div>
</template>
<script>
import UiDataTable from "@/UI/DataGrid/UIDataTable";
import UiDataGridPaginationNoLink from "@/UI/DataGrid/UIDataGridPaginationNoLink";

export default {
    name: "StoreDataGrid",

    components: { UiDataTable, UiDataGridPaginationNoLink },

    props: {
        stores: {
            type: Object,
            default: () => {},
        },
    },

    data() {
        return {
            paginator: {},
            table: {
                gridName: "store",
                fields: [
                    {
                        visible: true,
                        sortable: true,
                        type: "text",
                        link: true,
                        key: "name",
                        label: "Store",
                        events: {
                            click: "store_name_click",
                        },
                    },
                    {
                        visible: true,
                        sortable: false,
                        type: "text",
                        label: "Date Created",
                        key: "created_at",
                    },
                ],
            },
        };
    },

    mounted() {
        this.paginator = _.pick(this.stores, [
            "current_page",
            "from",
            "last_page",
            "per_page",
            "to",
            "total",
            "links",
        ]);
        this.emitter.on("store_name_click", (store) => {
            console.log(store);
        });
    },

    methods: {
        updatePagination(pagination) {
            this.paginator = pagination;
            this.search();
        },
        search() {
            this.$inertia.get("/store", {
                paginate: this.paginator,
            });
        },
    },
};
</script>
