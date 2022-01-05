<template>
    <div>Data Table</div>
    <div>{{ tableName }}</div>
</template>

<script>
import CollectionsEditTableMixin from "@/Pages/Collections/Mixins/CollectionsEditTableMixin";

export default {
    name: "CollectionsEditDataGrid",

    mixins: [CollectionsEditTableMixin],

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
};
</script>
