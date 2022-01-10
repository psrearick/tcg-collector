export default {
    data() {
        return {
            fieldRows: [
                {
                    row: 1,
                    fields: [
                        {
                            link: false,
                            visible: true,
                            sortable: false,
                            type: "component",
                            component: "BottomRowDropdownToggle",
                            label: "",
                            key: "",
                        },
                        {
                            visible: true,
                            sortable: true,
                            type: "composite-text",
                            link: false,
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
                            filterable: true,
                            type: "currency",
                            label: "Current",
                            key: "price",
                            queryComponent: "MinMax",
                            uiComponent: "ui-min-max",
                            uiComponentOptions: {
                                type: "currency",
                            },
                        },
                        {
                            visible: true,
                            type: "text",
                            label: "Quantity",
                            key: "quantity",
                        },
                    ],
                },
                {
                    row: 2,
                    fields: [
                        {
                            link: false,
                            visible: true,
                            sortable: true,
                            span: 6,
                            type: "component",
                            component: "BottomRowCollectionsEdit",
                            label: "Quantity",
                            key: "quantity",
                        },
                    ],
                },
            ],
            fields: [
                {
                    visible: true,
                    sortable: true,
                    type: "composite-text",
                    link: false,
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
                    queryComponent: "MinMax",
                    uiComponent: "ui-min-max",
                    uiComponentOptions: {
                        type: "currency",
                    },
                },
                {
                    visible: true,
                    sortable: true,
                    type: "component",
                    component: "HorizontalIncrementer",
                    label: "Quantity",
                    key: "quantity",
                },
            ],
            gridName: "collection-edit",
            selectMenu: [
                {
                    content: "Move to Collection",
                    action: "move_to_collection",
                },
                {
                    content: "Remove from Collection",
                    action: "remove_from_collection",
                },
            ],
        };
    },
    computed: {
        searchUrl() {
            return "/collections/" + this.collection.uuid + "/edit/list-search";
        },
        table() {
            if (!this.$settings.hasSettings()) {
                return {
                    fields: _.cloneDeep(this.fields),
                };
            }
            return {
                fields: [],
                fieldRows: this.fieldRows,
            };
        },
    },

    created() {
        this.emitter.on("incrementQuantity", (card) => {
            this.emitter.emit("updateCardQuantity", {
                change: 1,
                id: card.uuid,
                finish: card.finish,
                acquired_price: card.acquired_price,
                condition: card.condition,
            });
        });
        this.emitter.on("decrementQuantity", (card) => {
            this.emitter.emit("updateCardQuantity", {
                change: -1,
                id: card.uuid,
                finish: card.finish,
                acquired_price: card.acquired_price,
                condition: card.condition,
            });
        });
    },
};
