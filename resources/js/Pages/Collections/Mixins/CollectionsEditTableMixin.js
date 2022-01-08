export default {
    data() {
        return {
            bottomRow: [
                {
                    visible: true,
                    sortable: true,
                    span: 6,
                    type: "component",
                    component: "BottomRowCollectionsEdit",
                    label: "Quantity",
                    key: "quantity",
                },
            ],
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
                    queryComponent: "MinMax",
                    uiComponent: "ui-min-max",
                    uiComponentOptions: {
                        type: "currency",
                    },
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
            quantityIncrementer: {
                visible: true,
                sortable: true,
                type: "component",
                component: "HorizontalIncrementer",
                label: "Quantity",
                key: "quantity",
            },
        };
    },
    computed: {
        searchUrl() {
            return "/collections/" + this.collection.uuid + "/edit/list-search";
        },
        hasSettings() {
            const price_added = this.$page.props.user.settings[0]
                ? this.$page.props.user.settings[0].tracks_price || false
                : false;
            const card_condition = this.$page.props.user.settings[0]
                ? this.$page.props.user.settings[0].tracks_condition || false
                : false;
            return price_added || card_condition;
        },
        table() {
            if (!this.hasSettings) {
                let fields = _.cloneDeep(this.fields);
                fields.push(this.quantityIncrementer);
                return {
                    fields: fields,
                };
            }
            return {
                fields: [],
                fieldRows: [
                    {
                        row: 1,
                        fields: this.fields,
                    },
                    {
                        row: 2,
                        fields: this.bottomRow,
                    },
                ],
            };
        },
    },

    created() {
        this.emitter.on("incrementQuantity", (card) => {
            this.emitter.emit("updateCardQuantity", {
                change: 1,
                id: card.uuid,
                finish: card.finish,
            });
        });
        this.emitter.on("decrementQuantity", (card) => {
            this.emitter.emit("updateCardQuantity", {
                change: -1,
                id: card.uuid,
                finish: card.finish,
            });
        });
    },
};
