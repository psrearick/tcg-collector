export default {
    data() {
        return {
            table: {
                gridName: "printings-show",
                fields: [
                    {
                        visible: true,
                        type: "text",
                        link: true,
                        label: "Set",
                        key: "set_name",
                        events: {
                            click: "printing_set_click",
                        },
                        sortable: true,
                        filterable: false,
                    },
                    {
                        visible: true,
                        type: "text",
                        link: false,
                        label: "Rarity",
                        key: "rarity",
                        sortable: false,
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
                        key: "prices.Nonfoil",
                        sortable: true,
                        filterable: false,
                    },
                    {
                        visible: true,
                        type: "text",
                        link: false,
                        label: "Foil",
                        key: "prices.Foil",
                        sortable: true,
                        filterable: false,
                    },
                    {
                        visible: true,
                        type: "text",
                        link: false,
                        label: "Etched",
                        key: "prices.Etched",
                        sortable: true,
                        filterable: false,
                    },
                ],
            },
        };
    },
    created() {
        this.emitter.on("printing_set_click", (card) => {
            this.$inertia.get(`/cards/${card.uuid}`);
        });
    },
};
