<template>
    <ui-data-table
        :fields="table.fields"
        :grid-name="table.gridName"
        :data="card.printings"
    />
</template>

<script>
import UiDataTable from "../../../UI/DataGrid/UIDataTable";
export default {
    name: "PrintingsDataGrid",
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
