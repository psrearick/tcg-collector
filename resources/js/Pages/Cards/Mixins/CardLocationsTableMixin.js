export default {
    data() {
        return {
            cardLocationsTable: {
                gridName: "card-locations-show",
                fields: [
                    {
                        visible: true,
                        sortable: true,
                        type: "composite-text",
                        link: true,
                        key: "name",
                        label: "Collection",
                        values: [
                            {
                                key: "name",
                                classes: "",
                            },
                            {
                                key: "owner",
                                classes: "text-sm text-gray-500 pl-3",
                            },
                        ],
                        events: {
                            click: "collection_name_click",
                        },
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
        this.emitter.on("collection_name_click", (collection) => {
            const url = collection.owner ? "/group" : "/collections";
            this.$inertia.get(`${url}/${collection.uuid}`);
        });
    },
};
