export default {
    props: {
        page: {
            type: Object,
            default: () => {},
        },
    },
    mounted() {
        let clear = { searchResults: [] };
        this.$store.dispatch("addCardSearchResults", clear);
        this.$store.dispatch("addSetSearchResults", clear);
    },
    created() {
        const emitters = this.$store.getters.emitters;
        if (emitters.indexOf("updateCardQuantity") === -1) {
            this.$store.dispatch("addEmitter", "updateCardQuantity");
            this.emitter.on("updateCardQuantity", (change) => {
                this.updateCardQuantity(change);
            });
        }
    },
    methods: {
        updateCardQuantity: function (change) {
            let collection =
                this.$store.getters.currentCollection || this.page.collection;

            axios
                .post("/collections/" + collection.uuid + "/edit/add", change)
                .then((res) => {
                    const data = res.data;
                    if (data.error) {
                        return;
                    }

                    this.$store.dispatch("updateCardSearchResultsCard", data);
                    this.emitter.emit("trigger-collection-search");

                    this.$inertia.reload({
                        only: ["page"],
                    });
                });
        },
    },
};
