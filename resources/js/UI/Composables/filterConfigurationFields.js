import { ref } from "vue";
import { useStore } from "vuex";

export default function filterConfigurationFields(fields, gridName) {
    const store = useStore();

    const filterableFields = ref([]);
    filterableFields.value = fields.value.filter((field) => field.filterable);

    const filters = ref({});
    const getCurrentFilters = () => {
        const storeFilters = _.cloneDeep(store.getters.filters);
        let gridFilters = {};
        if (gridName.value in storeFilters) {
            gridFilters = storeFilters[gridName.value];
        }

        let filterables = _.keyBy(
            _.map(filterableFields.value, (field) => field.key),
            (filter) => filter
        );

        filters.value = _.mapValues(filterables, (filter) => {
            const field = fields.value.find((field) => field.key === filter);
            const gridFilter = gridFilters[filter] || {};

            return {
                field: filter,
                value: gridFilter.value,
                query_component: field.queryComponent,
            };
        });
    };

    return {
        filterableFields,
        getCurrentFilters,
        filters,
    };
}
