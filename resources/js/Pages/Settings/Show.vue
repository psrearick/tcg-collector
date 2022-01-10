<template>
    <app-layout title="Settings">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Settings
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <update-card-management-form :user="$page.props.auth.user" />
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import UpdateCardManagementForm from "@/Pages/Settings/Partials/UpdateCardManagementForm.vue";

export default defineComponent({
    components: {
        AppLayout,
        UpdateCardManagementForm,
    },

    props: {
        sessions: {
            type: Array,
            default: () => [],
        },
    },
});
</script>
