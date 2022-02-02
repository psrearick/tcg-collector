<template>
    <ui-data-table
        :fields="table.fields"
        :grid-name="'collection-' + table.gridName"
        :data="card.collections"
    />
</template>

<script>
import UiDataTable from "../../../UI/DataGrid/UIDataTable";
export default {
    name: "InCollectionDataGrid",
    components: { UiDataTable },
    props: {
        card: {
            type: Object,
            default: () => {},
        },
        table: {
            type: Object,
            default: () => {},
        },
    },
};
</script>
