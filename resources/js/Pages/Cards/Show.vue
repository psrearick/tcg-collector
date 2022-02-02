<template>
    <app-layout :title="card.name">
        <template #header>
            <div>
                <h2
                    class="
                        font-semibold
                        text-xl text-gray-800
                        leading-tight
                        py-2
                    "
                >
                    {{ card.name }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ card.set_name }}
                </p>
            </div>
        </template>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-8">
            <div>
                <div class="bg-primary-500 rounded-lg pt-2 mb-4">
                    <div class="bg-gray-100 rounded-lg border shadow-md">
                        <div
                            v-for="(price, finish) in card.prices"
                            :key="finish"
                            class="text-center"
                        >
                            <definition-list-item :title="finish">
                                <p class="text-sm text-gray-500">{{ price }}</p>
                            </definition-list-item>
                        </div>
                    </div>
                </div>
                <img
                    class="max-w-xs mx-auto"
                    :src="card.image"
                    :alt="card.name"
                />
            </div>
            <div>
                <card-list max-cols="2">
                    <card-list-card
                        v-for="(legality, format) in card.legalities"
                        :key="format"
                        :status="status(legality)"
                    >
                        <p class="text-sm font-medium text-gray-900 truncate">
                            {{ format }}
                        </p>
                        <p class="text-sm text-gray-500 truncate">
                            {{ legality }}
                        </p>
                    </card-list-card>
                </card-list>
            </div>
            <div class="bg-primary-500 rounded-lg pt-2">
                <div
                    class="
                        bg-white
                        shadow
                        overflow-hidden
                        rounded-lg
                        md:col-span-2
                        lg:col-span-1
                        lg:mt-0
                        h-full
                    "
                >
                    <div
                        class="
                            grid
                            md:grid-cols-2
                            bg-gray-100
                            py-2
                            border border-b-2
                        "
                    >
                        <div class="w-12 mx-auto">
                            <img :src="card.set_image" :alt="card.set_name" />
                        </div>
                        <div class="my-auto">
                            <p class="text-center md:text-left">
                                {{ card.set_name }} ({{
                                    card.set_code.toUpperCase()
                                }})
                            </p>
                        </div>
                    </div>

                    <dl class="mt-4">
                        <definition-list-item
                            title="Collector Number"
                            :value="card.collector_number"
                            border="border-t md:border-none border-gray-200"
                        />
                        <definition-list-item
                            title="Rarity"
                            :value="card.rarity"
                        />
                        <definition-list-item title="Type" :value="card.type" />
                        <definition-list-item title="Mana Cost">
                            <div v-html="manaCost" />
                        </definition-list-item>
                        <definition-list-item title="Oracle Text">
                            <div
                                class="text-sm"
                                v-html="`<span> ${text} </span>`"
                            />
                        </definition-list-item>
                        <definition-list-item
                            title="Language"
                            :value="card.language"
                        />
                        <definition-list-item
                            title="Artist"
                            :value="card.artist"
                        />
                    </dl>
                </div>
            </div>
        </div>
        <div class="mt-12">
            <p class="text-lg text-gray-700 mb-4">Printings</p>
            <printings-data-grid :card="card" :table="table" />
        </div>
        <div class="mt-12">
            <p class="text-lg text-gray-700 mb-4">In Collections</p>
            <in-collection-data-grid :card="card" :table="cardLocationsTable" />
        </div>
        <div class="mt-12">
            <p class="text-lg text-gray-700 mb-4">In Group</p>
            <in-group-data-grid :card="card" :table="cardLocationsTable" />
        </div>
    </app-layout>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import AppLayout from "../../Layouts/AppLayout.vue";
import CardList from "../../Components/CardLists/CardList";
import CardListCard from "../../Components/CardLists/CardListCard";
import DefinitionListItem from "./Partials/DefinitionListItem";
import PrintingsDataGrid from "./Partials/PrintingsDataGrid";
import PrintingsTableMixin from "./Mixins/PrintingsTableMixin";
import CardLocationsTableMixin from "./Mixins/CardLocationsTableMixin";
import InCollectionDataGrid from "./Partials/InCollectionDataGrid";
import InGroupDataGrid from "./Partials/InGroupDataGrid";
export default {
    name: "Show",

    components: {
        InGroupDataGrid,
        InCollectionDataGrid,
        PrintingsDataGrid,
        CardListCard,
        CardList,
        Link,
        AppLayout,
        DefinitionListItem,
    },

    mixins: [PrintingsTableMixin, CardLocationsTableMixin],

    props: {
        card: {
            type: Object,
            default: () => {},
        },
    },

    data() {
        return {
            text: "",
            manaCost: "",
        };
    },

    created() {
        let oracleText = this.$convertValue.replaceLineBreak(
            this.card.oracle_text
        );
        this.replaceSymbol(oracleText).then((res) => (this.text = res));
        this.replaceSymbol(this.card.mana_cost).then(
            (res) => (this.manaCost = res)
        );
    },

    methods: {
        status(status) {
            if (status === "Banned") {
                return "danger";
            }

            if (status === "Legal") {
                return "success";
            }

            return "warning";
        },
        async replaceSymbol(text) {
            return await this.$convertValue.replaceSymbol(text);
        },
    },
};
</script>
