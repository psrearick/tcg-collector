export default {
    data() {
        return {
            table: {
                fields: [
                    {
                        visible: true,
                        sortable: true,
                        type: "composite-text",
                        link: true,
                        key: "name",
                        label: "Card",
                        values: [
                            {
                                key: "name",
                                classes: "",
                            },
                            {
                                key: "finish",
                                classes: "text-sm text-gray-500 pl-3",
                            },
                        ],
                        events: {
                            click: "collection_card_name_click",
                        },
                    },
                    {
                        visible: true,
                        sortable: true,
                        type: "text",
                        link: false,
                        label: "Set",
                        key: "set",
                    },
                    {
                        visible: true,
                        type: "text",
                        label: "Features",
                        key: "features",
                    },
                    {
                        visible: true,
                        sortable: true,
                        type: "currency",
                        label: "Acquired Price",
                        key: "acquired_price",
                    },
                    {
                        visible: true,
                        sortable: true,
                        filterable: true,
                        type: "currency",
                        label: "Current",
                        key: "price",
                        uiComponent: "ui-min-max",
                        uiComponentOptions: {
                            type: "currency",
                        },
                    },
                    {
                        visible: true,
                        sortable: true,
                        type: "text",
                        label: "Quantity",
                        key: "quantity",
                    },
                ],
            },
            gridName: "collection-show",
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
                return fields[this.gridName];
            }

            return {};
        },
        sortFields() {
            let fields = this.$store.getters.sortFields;
            if (fields) {
                return fields[this.gridName];
            }

            return {};
        },
        filters() {
            let filters = this.$store.getters.filters;
            if (filters) {
                return filters[this.gridName];
            }

            return {};
        },
    },
    created() {
        this.emitter.on("collection_card_name_click", (card) => {
            this.$inertia.get(`/cards/${card.uuid}`);
        });
        this.emitter.on("sort", (gridName) => {
            if (gridName === this.gridName) {
                this.search();
            }
        });
    },
    methods: {
        setSort() {
            this.$store.dispatch("setSortFields", {
                gridName: this.gridName,
                fields: this.getObjectValue(this.search.sortQuery),
            });
            this.$store.dispatch("setSortOrder", {
                gridName: this.gridName,
                order: this.getObjectValue(this.search.sortOrder),
            });
            this.$store.dispatch("setFilters", {
                gridName: this.gridName,
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
