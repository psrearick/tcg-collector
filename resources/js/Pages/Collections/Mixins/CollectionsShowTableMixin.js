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
                        queryComponent: "MinMax",
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
                gridName: "collection-show",
            },
        };
    },
    computed: {
        searchUrl() {
            return "/collections/" + this.collection.uuid + "/edit/list-search";
        },
    },
};
