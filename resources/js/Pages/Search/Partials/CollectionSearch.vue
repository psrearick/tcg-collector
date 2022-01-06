<template>
    <div>
        <div>
            <span class="text-lg text-gray-500">Search All Collections</span>
        </div>
        <card-set-search
            v-model="cardSearchTerm"
            v-model:set-name="setSearchTerm"
            @gridConfigurationClick="
                gridConfigurationPanelShow = !gridConfigurationPanelShow
            "
        />
        <p v-if="searching" class="text-xs text-gray-400">Searching...</p>
        <ui-data-table
            v-if="cards.length"
            class="mt-4"
            :data="cards"
            :fields="cardsTable.fields"
            :grid-name="cardsTable.gridName"
        />
        <div v-if="collections.length" class="mt-8">
            <span class="text-base text-gray-500">Collections</span>
            <ui-data-table
                class="mt-4"
                :data="collections"
                :fields="collectionsTable.fields"
                :grid-name="collectionsTable.gridName"
            />
        </div>
        <ui-grid-configuration-panel
            v-model:show="gridConfigurationPanelShow"
            :fields="cardsTable.fields"
            :grid-name="cardsTable.gridName"
        />
    </div>
</template>
<script>
import CardSetSearch from "@/Pages/Cards/Partials/CardSetSearch";
import UiGridConfigurationPanel from "@/UI/DataGrid/UIGridConfigurationPanel";
import UiDataTable from "@/UI/DataGrid/UIDataTable";
import CollectionSearchTableMixin from "@/Pages/Search/Partials/CollectionSearchTableMixin";

export default {
    name: "CollectionSearch",

    components: { CardSetSearch, UiGridConfigurationPanel, UiDataTable },

    mixins: [CollectionSearchTableMixin],

    data() {
        return {
            cardSearchTerm: "",
            setSearchTerm: "",
            gridConfigurationPanelShow: false,
            searching: false,
            cards: [],
            collections: [],
        };
    },
    computed: {
        fieldSortOrder() {
            let fields = this.$store.getters.sortOrder;
            if (fields) {
                return fields[this.cardsTable.gridName];
            }

            return {};
        },
        sortFields() {
            let fields = this.$store.getters.sortFields;
            if (fields) {
                return fields[this.cardsTable.gridName];
            }

            return {};
        },
    },
    watch: {
        cardSearchTerm() {
            this.search();
        },
        setSearchTerm() {
            this.search();
        },
    },
    created() {
        this.emitter.on("sort", (gridName) => {
            if (gridName === this.cardsTable.gridName) {
                this.search();
            }
        });
    },
    mounted() {
        this.$store.dispatch("setSortFields", {
            gridName: this.cardsTable.gridName,
            fields: this.sortQuery || {},
        });
        this.$store.dispatch("setSortOrder", {
            gridName: this.cardsTable.gridName,
            order: this.sortOrder || {},
        });
    },
    methods: {
        showCard() {
            // this.$inertia.get(`/cards/cards/${id}`);
        },
        showCollection() {
            // this.$inertia.get(`/collections/collections/${id}`);
        },
        search: _.debounce(function () {
            this.cards = [];
            this.collections = [];
            this.searching = true;
            axios
                .post("/collections-search", {
                    card: this.cardSearchTerm,
                    set: this.setSearchTerm,
                    paginator: this.paginator,
                    sort: this.sortFields,
                    sortOrder: this.sortOrder,
                    filters: this.filters,
                })
                .then((res) => {
                    this.cards = res.data;
                    this.searching = false;
                });
        }, 1200),
    },
};
</script>
