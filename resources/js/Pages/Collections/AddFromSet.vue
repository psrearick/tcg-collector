<template>
    <app-layout title="Edit Collection - Add Set to Collection">
        <template #header>
            <div class="flex justify-between">
                <div>
                    <h2
                        class="
                            font-semibold
                            text-xl text-gray-800
                            leading-tight
                            py-2
                        "
                    >
                        Edit {{ collection.name }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        Add Cards to Collection by Set
                    </p>
                </div>
                <div class="flex space-x-4">
                    <Link :href="route('collections.show', collection.uuid)">
                        <ui-button
                            text="Done Editing"
                            button-style="primary-dark"
                        />
                    </Link>
                </div>
            </div>
        </template>
        <div class="flex justify-between flex-wrap">
            <h3 class="text-lg leading-6 font-medium text-gray-900 py-4">
                Select a Set
            </h3>
            <div class="py-4">
                <Link :href="route('collections.edit', [collection.uuid])">
                    <ui-button
                        text="Add Cards by Name"
                        button-style="success-outline"
                    />
                </Link>
            </div>
        </div>
        <div class="md:flex md:flex-wrap mb-4">
            <ui-search-select
                v-model:search-term="setSearchTerm"
                class="md:flex-1 max-w-full"
                :selected="selectedOption"
                label="Select a Set"
                :options="setOptions"
                :searching="setSearching"
                @update:selected-option="selectSet"
            />
            <div class="py-6">
                <ui-button
                    class="ml-2"
                    type="button"
                    text="Reset"
                    button-style="primary-outline"
                    @click="resetSetField"
                />
            </div>
        </div>

        <div v-if="selectedOption" class="w-full">
            <card-set-search
                v-model="cardSearchTerm"
                :set-search="false"
                :searching="dataGridSearching"
                :configure-table="false"
            />
            <collection-set-card-search-results :search="cardSearchTerm" />
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/inertia-vue3";
import UiButton from "@/UI/UIButton";
import UiSearchSelect from "@/UI/Form/SearchSelect/UISearchSelect";
import CollectionSetCardSearchResults from "@/Pages/Collections/Partials/CollectionSetCardSearchResults";
import CardSetSearch from "@/Pages/Cards/Partials/CardSetSearch";
import UpdateCardQuantityMixin from "@/Pages/Collections/Mixins/UpdateCardQuantityMixin";

export default {
    name: "AddFromSet",

    components: {
        AppLayout,
        Link,
        UiButton,
        UiSearchSelect,
        CardSetSearch,
        CollectionSetCardSearchResults,
    },

    mixins: [UpdateCardQuantityMixin],

    props: {
        collection: {
            type: Object,
            default: () => {},
        },
        sets: {
            type: Array,
            default: () => [],
        },
        selectedIndex: {
            type: Number,
            default: null,
        },
    },

    data() {
        return {
            setId: null,
            selectedOption: null,
            cardSearchTerm: "",
            setSearchTerm: "",
            setSearching: false,
            dataGridSearching: false,
            loaded: false,
            setOptions: [],
        };
    },

    watch: {
        cardSearchTerm() {
            if (this.loaded) {
                if (!this.setId) {
                    return;
                }
                this.searchCards();
            }
        },
        setSearchTerm() {
            if (this.loaded) {
                this.searchSets();
            }
        },
    },

    mounted() {
        this.$store.dispatch("updateCurrentCollection", {
            collection: this.collection,
        });

        this.loaded = true;
        this.mapSets(this.sets);
    },

    methods: {
        mapSets(sets) {
            this.setOptions = sets.map((set) => {
                return {
                    primary: set.name,
                    secondary: set.code.toUpperCase(),
                    id: set.id,
                };
            });
        },
        load: function (data) {
            this.loaded = true;
            this.$store.dispatch("addCardSearchResults", {
                searchResults: data,
            });
        },
        query: function () {
            axios
                .get(route("collection-set.edit", [this.collection.uuid]), {
                    params: {
                        set: this.setId,
                        cardSearch: this.cardSearchTerm,
                    },
                })
                .then((res) => {
                    this.dataGridSearching = false;
                    this.setSearching = false;
                    this.load(res.data);
                });
        },
        querySets: function () {
            axios
                .get(route("collection-set-search.index"), {
                    params: {
                        set: this.setSearchTerm,
                    },
                })
                .then((res) => {
                    this.setSearching = false;
                    this.mapSets(res.data);
                });
        },
        resetSetField() {
            this.setSearching = false;
            this.dataGridSearching = false;
            this.setId = null;
            this.setSearchTerm = "";
            this.cardSearchTerm = "";
            this.selectedOption = null;
            this.querySets();
            this.$store.dispatch("addCardSearchResults", {
                searchResults: [],
            });
        },
        searchCards: _.debounce(function () {
            this.dataGridSearching = true;
            this.query();
        }, 1200),
        searchSets: _.debounce(function () {
            this.setSearching = true;
            this.setOptions = [];
            this.querySets();
        }, 1200),
        selectSet: function (set) {
            this.selectedOption = set.index;
            this.setSearching = true;
            this.setId = set.id;
            this.query();
        },
    },
};
</script>
