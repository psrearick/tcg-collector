<template>
    <jet-form-section @submitted="updateCardManagement">
        <template #title> Card Management </template>

        <template #description>
            Update the available card management options.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <ui-toggle
                    v-model:enabled="form.price_added"
                    label="Custom Price Added"
                />

                <jet-input-error
                    :message="form.errors.price_added"
                    class="mt-2"
                />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <ui-toggle
                    v-model:enabled="form.card_condition"
                    label="Track Card Condition"
                />

                <jet-input-error
                    :message="form.errors.card_condition"
                    class="mt-2"
                />
            </div>
        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                <p class="text-primary-500">Saved.</p>
            </jet-action-message>

            <ui-button
                button-style="primary-outline"
                text="Save"
                type="submit"
                :disabled="form.processing"
            />
        </template>
    </jet-form-section>
</template>

<script>
import { defineComponent } from "vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import UiButton from "@/UI/UIButton";
import UiToggle from "@/UI/Form/UIToggle";

export default defineComponent({
    components: {
        JetActionMessage,
        JetFormSection,
        JetInputError,
        UiButton,
        UiToggle,
    },

    props: {
        user: {
            type: Object,
            default: () => {},
        },
    },

    data() {
        return {
            form: this.$inertia.form({
                _method: "PATCH",
                user_id: this.user.id,
                price_added: this.user.settings[0]
                    ? this.user.settings[0].tracks_price || false
                    : false,
                card_condition: this.user.settings[0]
                    ? this.user.settings[0].tracks_condition || false
                    : false,
            }),
        };
    },

    methods: {
        updateCardManagement() {
            this.form.post(route("settings.update-settings"), {
                errorBag: "updateSettings",
                preserveScroll: true,
            });
        },
    },
});
</script>
