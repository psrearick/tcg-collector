<template>
    <ui-data-table
        :fields="table.fields"
        :grid-name="table.gridName"
        :data="card.printings"
        active-key-field="uuid"
        :active-key-value="card.uuid"
        highlight-classes="bg-gray-100 ring-2 ring-offset-2 ring-primary-500 ring-inset"
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
