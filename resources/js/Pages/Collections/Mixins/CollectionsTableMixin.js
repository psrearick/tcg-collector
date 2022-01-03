export default {
    data() {
        return {
            default_paginator: {
                current_page: null,
                from: null,
                last_page: null,
                per_page: 15,
                to: null,
                total: null,
                links: [],
            },
            paginator: {
                current_page: null,
                from: null,
                last_page: null,
                per_page: 15,
                to: null,
                total: null,
                links: [],
            },
        };
    },
    computed: {
        sortOrder() {
            let fields = this.$store.getters.sortOrder;
            if (fields) {
                return fields[this.table.gridName];
            }

            return {};
        },
        sortFields() {
            let fields = this.$store.getters.sortFields;
            if (fields) {
                return fields[this.table.gridName];
            }

            return {};
        },
        filters() {
            let filters = this.$store.getters.filters;
            if (filters) {
                return filters[this.table.gridName];
            }

            return {};
        },
    },
    created() {
        this.emitter.on("collection_card_name_click", (card) => {
            this.$inertia.get(`/cards/${card.uuid}`);
        });
        this.emitter.on("sort", (gridName) => {
            if (gridName === this.table.gridName) {
                this.search();
            }
        });
        this.emitter.on("trigger-collection-search", () => {
            this.search();
        });
        this.emitter.on("move_to_collection", (data) => {
            this.moveToCollectionPanelData = data;
            this.moveToCollectionPanelShow = true;
        });
        this.emitter.on("remove_from_collection", (data) => {
            this.removeFromCollectionPanelData = data;
            this.removeFromCollectionPanelShow = true;
        });
    },
    methods: {
        setSort() {
            this.$store.dispatch("setSortFields", {
                gridName: this.table.gridName,
                fields: this.getObjectValue(this.search.sortQuery),
            });
            this.$store.dispatch("setSortOrder", {
                gridName: this.table.gridName,
                order: this.getObjectValue(this.search.sortOrder),
            });
            this.$store.dispatch("setFilters", {
                gridName: this.table.gridName,
                filters: this.getObjectValue(this.search.filters),
            });
        },
        getObjectValue(value) {
            if (!value) {
                return {};
            }

            if (Array.isArray(value)) {
                return {};
            }

            return value;
        },
    },
};
