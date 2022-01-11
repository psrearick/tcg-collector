<template>
    <div>
        <div v-if="hasSearch">
            <ui-card v-if="!hasResults && !searching">
                <p>No cards found!</p>
            </ui-card>
            <ui-well v-if="hasResults" class="mt-4">
                <div
                    v-for="(card, index) in cards"
                    :key="index"
                    class="
                        md:flex
                        justify-between
                        bg-white
                        rounded-md
                        w-full
                        py-2
                        px-4
                        mb-2
                    "
                >
                    <div class="h-48 px-4 text-center flex">
                        <img
                            v-if="card.image.length"
                            :src="card.image"
                            :alt="card.name"
                            class="h-full inline"
                        />
                        <div class="ml-8" style="margin-top: 5rem">
                            <img
                                v-if="card.set_image.length"
                                :src="card.set_image"
                                :alt="card.set"
                                class="h-8"
                            />
                        </div>
                    </div>
                    <div class="flex flex-col justify-between">
                        <div class="text-center">
                            <p>
                                {{ card.name }}
                                <span v-if="card.set_name">
                                    - {{ card.set_name }}
                                </span>
                                <span v-if="card.set_code">
                                    ({{ card.set_code }})
                                </span>
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ card.features }}
                            </p>
                        </div>
                        <div class="flex justify-center py-4">
                            <ui-vertical-incrementer
                                v-for="(finish, finishIndex) in card.finishes"
                                :key="finishIndex"
                                class="md:mx-4"
                                :label="finish"
                                :model-value="card.quantities[finish]"
                                :active="
                                    activeField === index &&
                                    activeFieldFinish === finish
                                "
                                @activate="activate(index, finish)"
                                @update:model-value="
                                    updateQuantity($event, card.uuid, finish)
                                "
                            />
                        </div>
                    </div>
                    <div>
                        <p
                            v-for="(price, priceIndex) in getPrices(
                                card.prices
                            )"
                            :key="priceIndex"
                            class="text-center md:text-right md:pr-1"
                        >
                            <span class="text-sm text-gray-500 mr-2">{{
                                capitalizeFirstLetter(priceIndex)
                            }}</span>
                            <span>{{ price ? price : "N/A" }}</span>
                        </p>
                    </div>
                </div>
            </ui-well>
        </div>
        <ui-data-grid-pagination-no-link
            v-if="hasResults"
            :pagination="pagination"
            @update:pagination="updatePage"
        />
    </div>
</template>

<script>
import UiDataGridPaginationNoLink from "@/UI/DataGrid/UIDataGridPaginationNoLink";
import UiVerticalIncrementer from "@/UI/Buttons/UIVerticalIncrementer";
import UiCard from "@/UI/UICard";
import UiWell from "@/UI/UIWell";

export default {
    name: "CollectionCardSearchResults",

    components: {
        UiDataGridPaginationNoLink,
        UiCard,
        UiWell,
        UiVerticalIncrementer,
    },

    props: {
        paginator: {
            type: Object,
            default: () => {},
        },
        search: {
            type: Object,
            default: () => {},
        },
        searching: {
            type: Boolean,
            default: false,
        },
    },

    emits: ["update:paginator"],

    data() {
        return {
            activeField: null,
            activeFieldFinish: null,
        };
    },

    computed: {
        cards() {
            return this.$store.getters.cardSearchResults;
        },
        cardsLength() {
            if (!this.cards) {
                return 0;
            }

            return Object.keys(this.cards).length;
        },
        hasResults() {
            return this.cardsLength > 0;
        },
        hasSearch() {
            return this.search.card.length > 0 || this.search.set.length > 0;
        },
        pagination() {
            return this.paginator ? this.paginator : {};
        },
    },

    methods: {
        activate(id, finish) {
            this.activeField = id;
            this.activeFieldFinish = finish;
        },
        getPrices(prices) {
            let result = {};
            Object.keys(prices).forEach((price) => {
                if ("display_" + price in prices) {
                    result[price] = prices["display_" + price];
                }
            });
            return result;
        },
        updatePage(page) {
            this.$emit("update:paginator", page);
        },
        capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        },
        updateQuantity(value, id, finish) {
            this.activeField = null;
            this.activeFieldFinish = null;
            let change =
                value -
                (this.cards.find((card) => card.uuid === id)["quantities"][
                    finish
                ] || 0);
            if (change === 0) {
                return;
            }

            this.emitter.emit("updateCardQuantity", {
                change: change,
                id: id,
                finish: finish,
            });
        },
    },
};
</script>
