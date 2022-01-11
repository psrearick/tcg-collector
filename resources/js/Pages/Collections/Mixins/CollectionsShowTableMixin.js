import _ from "lodash";

export default {
    data() {
        return {
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
                    type: "text",
                    label: "Acquired Price",
                    key: "display_acquired_price",
                },
                {
                    visible: true,
                    sortable: true,
                    filterable: true,
                    type: "text",
                    label: "Current",
                    key: "display_price",
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
            fieldRows: [
                {
                    row: 1,
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
                            type: "text",
                            label: "Acquired Price",
                            key: "display_acquired_price",
                        },
                        {
                            visible: true,
                            sortable: true,
                            filterable: true,
                            type: "text",
                            label: "Current",
                            key: "display_price",
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
                },
                {
                    row: 2,
                    fields: [
                        {
                            link: false,
                            visible: true,
                            sortable: true,
                            span: 7,
                            type: "component",
                            component: "BottomRowCollectionsShow",
                            label: "Quantity",
                            key: "quantity",
                        },
                    ],
                },
            ],
            gridName: "collection-show",
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
                    gridName: this.gridName,
                };
            }
            return {
                fields: [],
                fieldRows: _.cloneDeep(this.fieldRows),
                gridName: this.gridName,
            };
        },
    },
};
