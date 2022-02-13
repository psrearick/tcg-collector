import { ref, computed } from "vue";
import { useStore } from "vuex";

export default function sortConfigurationFields(fields, gridName) {
    const store = useStore();

    const sortFields = ref({});
    const getCurrentSortFields = () => {
        const storeSorts = _.cloneDeep(store.getters.sortFields) || {};
        if (gridName.value in storeSorts) {
            sortFields.value = storeSorts[gridName.value];
        }
    };

    const sortOrder = ref({});
    const getCurrentSortOrder = () => {
        const storeOrder = _.cloneDeep(store.getters.sortOrder) || {};
        let order = {};
        if (gridName.value in storeOrder) {
            order = storeOrder[gridName.value];
        }

        let sortables = _.map(
            fields.value.filter((field) => {
                return field.sortable && field.visible;
            }),
            "key"
        );

        sortables.sort((a, b) => {
            let aPos = a in order ? order[a] : -1;
            let bPos = b in order ? order[b] : -1;

            if (aPos === bPos) {
                return 0;
            }

            if (aPos === -1) {
                return 1;
            }

            if (bPos === -1) {
                return -1;
            }

            return bPos < aPos ? 1 : -1;
        });

        let fieldsOrdered = {};
        sortables.forEach((field, index) => {
            fieldsOrdered[field] = index;
        });

        sortOrder.value = fieldsOrdered;
    };

    const sortableFields = computed(() => {
        let sortingFields = fields.value
            .filter((field) => field.sortable && field.visible)
            .map((field) => {
                field.sortDirection = null;
                if (sortFields.value) {
                    if (field.key in sortFields.value) {
                        field.sortDirection = sortFields.value[field.key];
                    }
                }

                field.sortOrder = sortOrder.value[field.key];

                return field;
            });

        return _.sortBy(sortingFields, (field) => {
            return field.sortOrder;
        });
    });

    const updateSort = (field) => {
        let fieldname = field["key"];
        let direction = "asc";

        if (fieldname in sortFields.value) {
            direction = "desc";
            if (sortFields.value[fieldname] === "desc") {
                delete sortFields.value[fieldname];
                direction = null;
            }
        }

        if (direction) {
            sortFields.value[fieldname] = direction;
        }
    };

    const moveUp = (field) => {
        let currentPosition = field.sortOrder;
        let newPosition = currentPosition - 1;
        changePosition(currentPosition, newPosition);
    };

    const moveDown = (field) => {
        let currentPosition = field.sortOrder;
        let newPosition = currentPosition + 1;
        changePosition(currentPosition, newPosition);
    };

    const changePosition = (currentPosition, newPosition) => {
        let changed = [];
        Object.keys(sortOrder.value).forEach((key) => {
            if (
                sortOrder.value[key] === currentPosition &&
                changed.indexOf(key) === -1
            ) {
                sortOrder.value[key] = newPosition;
                changed.push(key);
                return;
            }
            if (
                sortOrder.value[key] === newPosition &&
                changed.indexOf(key) === -1
            ) {
                sortOrder.value[key] = currentPosition;
                changed.push(key);
            }
        });
    };

    return {
        sortFields,
        sortOrder,
        sortableFields,
        getCurrentSortFields,
        getCurrentSortOrder,
        updateSort,
        moveUp,
        moveDown,
    };
}
