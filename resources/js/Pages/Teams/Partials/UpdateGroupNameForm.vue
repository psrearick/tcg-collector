<template>
    <jet-form-section @submitted="updateGroupName">
        <template #title> Group Name </template>

        <template #description>
            The group's name and owner information.
        </template>

        <template #form>
            <!-- Group Owner Information -->
            <div class="col-span-6">
                <jet-label value="Group Owner" />

                <div class="flex items-center mt-2">
                    <img
                        class="w-12 h-12 rounded-full object-cover"
                        :src="team.owner.profile_photo_url"
                        :alt="team.owner.name"
                    />

                    <div class="ml-4 leading-tight">
                        <div>{{ team.owner.name }}</div>
                        <div class="text-gray-700 text-sm">
                            {{ team.owner.email }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Group Name -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="name" value="Group Name" />

                <jet-input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    :disabled="!permissions.canUpdateTeam"
                />

                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>
        </template>

        <template v-if="permissions.canUpdateTeam" #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>

            <ui-button
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                button-style="primary-dark"
                type="submit"
            >
                Save
            </ui-button>
        </template>
    </jet-form-section>
</template>

<script>
import { defineComponent } from "vue";
import JetActionMessage from "@/Jetstream/ActionMessage";
import JetFormSection from "@/Jetstream/FormSection";
import JetInput from "@/Jetstream/Input";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import UiButton from "@/UI/UIButton";

export default defineComponent({
    components: {
        UiButton,
        JetActionMessage,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
    },

    props: ["team", "permissions"],

    data() {
        return {
            form: this.$inertia.form({
                name: this.team.name,
            }),
        };
    },

    methods: {
        updateGroupName() {
            this.form.put(route("teams.update", this.team), {
                errorBag: "updateTeamName",
                preserveScroll: true,
            });
        },
    },
});
</script>
