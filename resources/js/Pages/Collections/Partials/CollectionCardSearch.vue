<template>
    <card-set-search
        v-model="cardSearchTerm"
        v-model:set-name="setSearchTerm"
        :configure-table="false"
    />
    <p v-if="searching" class="text-xs text-gray-400">Searching...</p>
    <collection-card-search-results
        :paginator="paginator"
        :search="searchTerms"
        :searching="searching"
        @update:paginator="updatePage"
    />
</template>

<script>
import CardSetSearch from "../../../Pages/Cards/Partials/CardSetSearch";
import CollectionCardSearchResults from "../../../Pages/Collections/Partials/CollectionCardSearchResults";
export default {
    name: "CollectionCardSearch",

    components: {
        CardSetSearch,
        CollectionCardSearchResults,
    },

    data() {
        return {
            cardSearchTerm: "",
            setSearchTerm: "",
            searching: false,
            default_paginator: {
                current_page: null,
                from: null,
                last_page: null,
                per_page: 15,
                to: null,
                total: null,
                links: [],
            },
            paginator: {
                current_page: null,
                from: null,
                last_page: null,
                per_page: 15,
                to: null,
                total: null,
                links: [],
            },
        };
    },

    computed: {
        searchTerms() {
            return {
                card: this.cardSearchTerm,
                set: this.setSearchTerm,
            };
        },
    },

    watch: {
        cardSearchTerm(val) {
            this.paginator = this.default_paginator;
            if (!val) {
                this.$store.dispatch("addCardSearchResults", {
                    searchResults: val,
                });
                return;
            }
            this.query();
        },
        setSearchTerm(val) {
            this.paginator = this.default_paginator;
            if (!val) {
                this.$store.dispatch("addCardSearchResults", {
                    searchResults: val,
                });
                return;
            }
            this.query();
        },
    },

    methods: {
        query: _.debounce(function () {
            this.searching = true;
            this.runSearchResults([]);
            const collectionUuid = this.$store.getters.currentCollection.uuid;
            axios
                .post("/collections/" + collectionUuid + "/edit/search", {
                    card: this.cardSearchTerm,
                    set: this.setSearchTerm,
                    paginator: this.paginator,
                })
                .then((res) => {
                    this.searching = false;
                    this.runSearchResults(res.data.data);
                    this.paginator = _.pick(res.data, [
                        "current_page",
                        "from",
                        "last_page",
                        "per_page",
                        "to",
                        "total",
                        "links",
                    ]);
                });
        }, 1200),
        runSearchResults(val) {
            this.$store.dispatch("addCardSearchResults", {
                searchResults: val,
            });
        },
        updatePage(paginator) {
            this.paginator = paginator;
            this.query();
        },
    },
};
</script>
