<template>
    <app-layout title="Search">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Search
            </h2>
            <p class="text-sm text-gray-500">
                Search cards in all of the collections in your group.
            </p>
        </template>
        <div>
            <collection-search
                search-url="/group/search"
                collection-url="/group"
            />
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import CollectionSearch from "@/Pages/Search/Partials/CollectionSearch";

export default {
    name: "Search",

    components: { CollectionSearch, AppLayout },

    title: "MTG Collector - Dashboard",

    header: "MTG Collector",
};
</script>
