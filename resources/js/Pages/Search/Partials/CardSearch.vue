<template>
    <div>
        <!--        <ui-checkbox-->
        <!--            v-model:checked="allPrintings"-->
        <!--            name="allPrintings"-->
        <!--            label="Display all printings"-->
        <!--            class="mb-4"-->
        <!--        />-->
        <card-set-search
            v-model="cardSearchTerm"
            v-model:set-name="setSearchTerm"
            :configure-table="true"
            @gridConfigurationClick="
                gridConfigurationPanelShow = !gridConfigurationPanelShow
            "
        />
        <p v-if="searching" class="text-xs text-gray-400">Searching...</p>
        <ui-data-table
            v-if="cards.length"
            class="mt-4"
            :data="cards"
            :fields="table.fields"
            :grid-name="table.gridName"
        />
        <ui-data-grid-pagination-no-link
            :pagination="cardsPaginator"
            @update:pagination="updateCardsPagination"
        />
        <ui-grid-configuration-panel
            v-model:show="gridConfigurationPanelShow"
            :fields="table.fields"
            :grid-name="table.gridName"
        />
    </div>
</template>

<script>
import UiCheckbox from "@/UI/Form/UICheckbox";
import CardSetSearch from "@/Pages/Cards/Partials/CardSetSearch";
import UiDataTable from "@/UI/DataGrid/UIDataTable";
import UiDataGridPaginationNoLink from "@/UI/DataGrid/UIDataGridPaginationNoLink";
import CardSearchTableMixin from "@/Pages/Search/Mixins/CardSearchTableMixin";
import UiGridConfigurationPanel from "@/UI/DataGrid/UIGridConfigurationPanel";
export default {
    name: "CardSearch",

    components: {
        UiGridConfigurationPanel,
        UiDataGridPaginationNoLink,
        UiDataTable,
        CardSetSearch,
        UiCheckbox,
    },

    mixins: [CardSearchTableMixin],

    data() {
        return {
            gridConfigurationPanelShow: false,
            cardSearchTerm: "",
            setSearchTerm: "",
            allPrintings: false,
            cards: [],
            cardsPaginator: [],
            searching: false,
        };
    },

    computed: {
        fieldSortOrder() {
            let fields = this.$store.getters.sortOrder;
            if (fields) {
                return fields[this.table.gridName];
            }

            return {};
        },
        sortFields() {
            let fields = this.$store.getters.sortFields;
            if (fields) {
                return fields[this.table.gridName];
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
            if (gridName === this.table.gridName) {
                this.search();
            }
        });
    },

    mounted() {
        this.$store.dispatch("setSortFields", {
            gridName: this.table.gridName,
            fields: this.sortQuery || {},
        });
        this.$store.dispatch("setSortOrder", {
            gridName: this.table.gridName,
            order: this.sortOrder || {},
        });
    },

    methods: {
        processData(res) {
            this.cards = res.data.data;
            this.cardsPaginator = _.pick(res.data, [
                "current_page",
                "from",
                "last_page",
                "per_page",
                "to",
                "total",
                "links",
            ]);
        },
        search: _.debounce(function () {
            this.cards = [];
            this.searching = true;
            axios
                .post("/cards-search", {
                    card: this.cardSearchTerm,
                    set: this.setSearchTerm,
                    paginator: this.cardsPaginator,
                    sort: this.sortFields,
                    sortOrder: this.fieldSortOrder,
                })
                .then((res) => {
                    this.processData(res.data);
                    this.searching = false;
                });
        }, 1200),
        showCard(uuid) {
            this.$inertia.get(`/cards/${uuid}`);
        },
        showCollection(uuid) {
            this.$inertia.get(`${this.collectionUrl}/${uuid}`);
        },
        updateCardsPagination(pagination) {
            this.cardsPaginator = pagination;
            this.search();
        },
    },
};
</script>
