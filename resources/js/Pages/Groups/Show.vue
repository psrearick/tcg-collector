<template>
    <app-layout title="Collections">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight py-2">
                {{ $page.props.auth.user.current_team.name }}
            </h2>
        </template>
        <div>
            <users-list :users="users" />
        </div>
        <div class="mt-4">
            <div
                v-for="(collection, collectionIndex) in collections"
                :key="collectionIndex"
            >
                {{ collection.name }},
                {{ collection.summary_data.current_value }},
                {{ collection.user.name }}
            </div>
        </div>
    </app-layout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import UsersList from "@/Pages/Groups/Partials/UsersList";

export default {
    name: "Show",

    components: {
        AppLayout,
        UsersList,
    },

    props: {
        collections: {
            type: Array,
            default: () => [],
        },
        users: {
            type: Array,
            default: () => [],
        },
    },
};
</script>
