<template>
    <app-layout title="Create Store">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight py-2">
                Create Store
            </h2>
        </template>
        <template #headerRight>
            <Link :href="route('stores.index')">
                <ui-button text="Cancel" button-style="primary-dark" />
            </Link>
        </template>
        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <create-store-form />
            </div>
        </div>
    </app-layout>
</template>
<script>
import { Link } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import UiButton from "@/UI/UIButton";
import CreateStoreForm from "@/Pages/Stores/Partials/CreateStoreForm";

export default {
    name: "Create",

    components: { AppLayout, Link, UiButton, CreateStoreForm },

    props: {
        stores: {
            type: Object,
            default: () => {},
        },
    },
};
</script>
