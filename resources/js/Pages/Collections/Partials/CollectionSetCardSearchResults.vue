<template>
    <div>
        <ui-card v-if="!hasResults">
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
                <div class="flex flex-col">
                    <p class="text-center">#{{ card.collector_number }}</p>
                    <div class="h-48 px-4 text-center">
                        <img
                            v-if="card.image.length"
                            :src="card.image"
                            :alt="card.name"
                            class="h-full inline"
                        />
                    </div>
                </div>
                <div class="flex flex-col justify-between">
                    <div class="text-center">
                        <p>{{ card.name }}</p>
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
                        v-for="(price, priceIndex) in getPrices(card.prices)"
                        :key="priceIndex"
                        class="text-center md:text-right"
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
</template>

<script>
import UiVerticalIncrementer from "@/UI/Buttons/UIVerticalIncrementer";
import UiCard from "@/UI/UICard";
import UiWell from "@/UI/UIWell";

export default {
    name: "CollectionSetCardSearchResults",

    components: {
        UiCard,
        UiWell,
        UiVerticalIncrementer,
    },

    props: {
        search: {
            type: String,
            default: "",
        },
    },

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
            return Object.keys(this.cards).length;
        },
        hasResults() {
            return this.cardsLength > 0;
        },
        hasSearch() {
            return this.search.length > 0;
        },
    },

    methods: {
        activate(id, finish) {
            this.activeField = id;
            this.activeFieldFinish = finish;
        },
        capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
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
        updateQuantity: function (value, id, finish) {
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
