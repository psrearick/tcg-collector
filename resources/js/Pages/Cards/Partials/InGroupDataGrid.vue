<template>
    <ui-data-table
        :fields="table.fields"
        :grid-name="'group-' + table.gridName"
        :data="card.group_collections"
    />
</template>

<script>
import UiDataTable from "../../../UI/DataGrid/UIDataTable";
export default {
    name: "InGroupDataGrid",
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
