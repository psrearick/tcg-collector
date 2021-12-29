<template>
    <jet-form-section @submitted="createGroup">
        <template #title> Group Details </template>

        <template #description>
            Create a new group with which to share collections.
        </template>

        <template #form>
            <div class="col-span-6">
                <jet-label value="Group Owner" />

                <div class="flex items-center mt-2">
                    <img
                        class="object-cover w-12 h-12 rounded-full"
                        :src="$page.props.user.profile_photo_url"
                        :alt="$page.props.user.name"
                    />

                    <div class="ml-4 leading-tight">
                        <div>{{ $page.props.user.name }}</div>
                        <div class="text-sm text-gray-700">
                            {{ $page.props.user.email }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="name" value="Group Name" />
                <jet-input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="block w-full mt-1"
                    autofocus
                />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <jet-button
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Create
            </jet-button>
        </template>
    </jet-form-section>
</template>

<script>
import { defineComponent } from "vue";
import JetButton from "@/Jetstream/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";

export default defineComponent({
    components: {
        JetButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
    },

    data() {
        return {
            form: this.$inertia.form({
                name: "",
            }),
        };
    },

    methods: {
        createGroup() {
            this.form.post(route("teams.store"), {
                errorBag: "createTeam",
                preserveScroll: true,
            });
        },
    },
});
</script>
