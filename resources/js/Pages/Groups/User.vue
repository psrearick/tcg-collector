<template>
    <app-layout title="User">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight py-2">
                {{ groupUser.name }}
            </h2>
        </template>
        <div>Data Grid</div>
    </app-layout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";

export default {
    name: "User",

    components: {
        AppLayout,
    },

    props: {
        groupUser: {
            type: Object,
            default: () => {},
        },
    },
};
</script>
