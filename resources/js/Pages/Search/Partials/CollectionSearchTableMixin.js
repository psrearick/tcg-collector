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
                        key: "set_name",
                        event: "set_name_click",
                        sortable: true,
                        filterable: false,
                    },
                    {
                        visible: true,
                        type: "text",
                        link: false,
                        label: "Features",
                        key: "features",
                        sortable: false,
                        filterable: false,
                    },
                    {
                        visible: true,
                        type: "text",
                        link: false,
                        label: "Non-Foil",
                        key: "quantities.nonfoil",
                        sortable: true,
                        filterable: false,
                    },
                    {
                        visible: true,
                        type: "text",
                        link: false,
                        label: "Foil",
                        key: "quantities.foil",
                        sortable: true,
                        filterable: false,
                    },
                    {
                        visible: true,
                        type: "text",
                        link: false,
                        label: "Etched",
                        key: "quantities.etched",
                        sortable: true,
                        filterable: false,
                    },
                    {
                        visible: true,
                        type: "text",
                        label: "Total",
                        key: "quantities.total",
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
                        label: "Collection",
                        key: "collection.name",
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
                        key: "quantities.nonfoil",
                        sortable: true,
                        filterable: false,
                    },
                    {
                        visible: true,
                        type: "text",
                        link: false,
                        label: "Foil",
                        key: "quantities.foil",
                        sortable: true,
                        filterable: false,
                    },
                    {
                        visible: true,
                        type: "text",
                        link: false,
                        label: "Etched",
                        key: "quantities.etched",
                        sortable: true,
                        filterable: false,
                    },
                    {
                        visible: true,
                        type: "text",
                        label: "Total Collected",
                        key: "quantities.total",
                        sortable: true,
                        filterable: false,
                    },
                ],
            },
        };
    },
    created() {
        this.emitter.on("view_collection_card_details", (card) => {
            this.collections = Object.values(card.collected);
        });
        this.emitter.on("card_name_click", (card) => {
            this.showCard(card.id);
        });
        this.emitter.on("collection_name_click", (collection) => {
            this.showCollection(collection.collection.uuid);
        });
    },
};
