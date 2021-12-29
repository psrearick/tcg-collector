<template>
    <app-layout title="Group Settings">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Group Settings
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <update-group-name-form
                    :team="team"
                    :permissions="permissions"
                />

                <group-member-manager
                    class="mt-10 sm:mt-0"
                    :team="team"
                    :available-roles="availableRoles"
                    :user-permissions="permissions"
                />

                <template
                    v-if="permissions.canDeleteTeam && !team.personal_team"
                >
                    <jet-section-border />

                    <delete-group-form class="mt-10 sm:mt-0" :team="team" />
                </template>
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import DeleteGroupForm from "@/Pages/Teams/Partials/DeleteGroupForm.vue";
import JetSectionBorder from "@/Jetstream/SectionBorder.vue";
import GroupMemberManager from "@/Pages/Teams/Partials/GroupMemberManager.vue";
import UpdateGroupNameForm from "@/Pages/Teams/Partials/UpdateGroupNameForm.vue";

export default defineComponent({
    components: {
        AppLayout,
        DeleteGroupForm,
        JetSectionBorder,
        GroupMemberManager,
        UpdateGroupNameForm,
    },
    props: ["team", "availableRoles", "permissions"],
});
</script>
