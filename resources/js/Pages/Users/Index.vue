<template>
    <app-layout title="Stores">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight py-2">
                Users
            </h2>
        </template>
        <div>Users</div>
    </app-layout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";

export default {
    name: "Index",

    components: { AppLayout },
};
</script>
