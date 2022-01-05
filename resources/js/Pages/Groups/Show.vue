<template>
    <app-layout title="Collections">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight py-2">
                {{ $page.props.auth.user.current_team.name }}
            </h2>
        </template>
        <div>Group
            {{ collections }}
        </div>
    </app-layout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";

export default {
    name: "Show",

    components: {
        AppLayout,
    },

    props: {
        collections: {
            type: Array,
            default: () => {},
        },
    },
};
</script>
