<template>
    <app-layout title="Create Group">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Group
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <create-group-form />
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import CreateGroupForm from "@/Pages/Teams/Partials/CreateGroupForm.vue";

export default defineComponent({
    components: {
        AppLayout,
        CreateGroupForm,
    },
});
</script>
