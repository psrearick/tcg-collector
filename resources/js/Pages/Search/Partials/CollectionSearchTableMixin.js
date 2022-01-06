export default {
    data() {
        return {
            cardsTable: {
                gridName: "dashboard-cards",
                fields: [
                    {
                        visible: true,
                        type: "text",
                        link: true,
                        hover: true,
                        label: "Card",
                        key: "name",
                        events: {
                            click: "card_name_click",
                            hover: "card_name_hover",
                        },
                        sortable: true,
                        filterable: false,
                    },
                    {
                        visible: true,
                        type: "text",
                        link: false,
                        label: "Set",
                        key: "set",
                        event: "set_name_click",
                        sortable: true,
                        filterable: false,
                    },
                    {
                        visible: true,
                        type: "text",
                        link: false,
                        label: "Features",
                        key: "feature",
                        sortable: false,
                        filterable: false,
                    },
                    {
                        visible: true,
                        type: "text",
                        link: false,
                        label: "Non-Foil",
                        key: "quantity_nonfoil",
                        sortable: true,
                        filterable: false,
                    },
                    {
                        visible: true,
                        type: "text",
                        link: false,
                        label: "Foil",
                        key: "quantity_foil",
                        sortable: true,
                        filterable: false,
                    },
                    {
                        visible: true,
                        type: "text",
                        link: false,
                        label: "Etched",
                        key: "quantity_etched",
                        sortable: true,
                        filterable: false,
                    },
                    {
                        visible: true,
                        type: "text",
                        label: "Total Collected",
                        key: "quantity",
                        sortable: true,
                        filterable: false,
                    },
                    {
                        visible: true,
                        type: "component",
                        component: "UiButton",
                        label: "",
                        link: true,
                        events: {
                            click: "view_collection_card_details",
                        },
                        props: {
                            text: "Details",
                            "button-style": "primary-outline",
                        },
                    },
                ],
            },
            collectionsTable: {
                collectionsGridName: "dashboard-collections",
                fields: [
                    {
                        visible: true,
                        type: "text",
                        link: true,
                        hover: true,
                        label: "Card",
                        key: "name",
                        events: {
                            click: "collection_name_click",
                        },
                        sortable: true,
                        filterable: false,
                    },
                    {
                        visible: true,
                        type: "text",
                        link: false,
                        label: "Non-Foil",
                        key: "nonfoil",
                        sortable: true,
                        filterable: false,
                    },
                    {
                        visible: true,
                        type: "text",
                        link: false,
                        label: "Foil",
                        key: "foil",
                        sortable: true,
                        filterable: false,
                    },
                    {
                        visible: true,
                        type: "text",
                        link: false,
                        label: "Etched",
                        key: "etched",
                        sortable: true,
                        filterable: false,
                    },
                    {
                        visible: true,
                        type: "text",
                        label: "Total Collected",
                        key: "total",
                        sortable: true,
                        filterable: false,
                    },
                ],
            },
        };
    },
    created() {
        this.emitter.on("view_collection_card_details", (card) => {
            this.collections = Object.values(card.collections);
        });
        this.emitter.on("card_name_click", (card) => {
            this.showCard(card.id);
        });
        this.emitter.on("collection_name_click", (collection) => {
            this.showCollection(collection.id);
        });
    },
};
