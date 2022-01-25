<template>
    <app-layout title="Stores">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight py-2">
                Stores
            </h2>
        </template>
        <template #headerRight>
            <Link :href="route('stores.create')">
                <ui-button text="Create Store" button-style="primary-dark" />
            </Link>
        </template>
        <store-data-grid :stores="stores" />
    </app-layout>
</template>
<script>
import { Link } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import UiButton from "@/UI/UIButton";
import StoreDataGrid from "@/Pages/Stores/Partials/StoreDataGrid";

export default {
    name: "Index",

    components: { AppLayout, Link, UiButton, StoreDataGrid },

    props: {
        stores: {
            type: Object,
            default: () => {},
        },
    },
};
</script>
