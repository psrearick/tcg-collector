<template>
    <app-layout title="Search">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Search
            </h2>
        </template>

        <div class="px-4 md:px-6 lg:px-8">
            <div class="mb-8">
                <span class="text-lg text-gray-500">Search Scope</span>
                <ui-radio-card-list
                    key="key"
                    v-model:value="scopeSelected"
                    class="my-2"
                    :options="scopeOptions"
                />
            </div>

            <div v-if="scopeSelected === 'collections'">
                <span class="mb-4 block text-lg text-gray-500">
                    Search in all Collections
                </span>
                <div><collection-search /></div>
            </div>

            <div v-if="scopeSelected === 'group'">
                <span class="mb-4 block text-lg text-gray-500">
                    Search in Current Group
                </span>
                <collection-search
                    search-url="/group/search"
                    collection-url="/group"
                />
            </div>

            <div v-if="scopeSelected === 'all'">
                <span class="mb-4 block text-lg text-gray-500">
                    Search All Cards
                </span>
                <card-search />
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import CollectionSearch from "@/Pages/Search/Partials/CollectionSearch";
import UiRadioCardList from "@/UI/Form/UIRadioCardList";
import CardSearch from "@/Pages/Search/Partials/CardSearch";

export default {
    name: "Show",

    components: { CardSearch, UiRadioCardList, CollectionSearch, AppLayout },

    title: "MTG Collector - Dashboard",

    header: "MTG Collector",

    data() {
        return {
            scopeOptions: [
                {
                    key: "collections",
                    label: "Your Cards",
                    description: "Search for cards in your collections",
                },
                {
                    key: "group",
                    label: "Your Current Group",
                    description:
                        "Search for cards within the collections shared in your group",
                },
                {
                    key: "all",
                    label: "All Cards",
                    description: "Search all cards",
                },
            ],
            scopeSelected: null,
            allPrintings: false,
        };
    },
};
</script>
