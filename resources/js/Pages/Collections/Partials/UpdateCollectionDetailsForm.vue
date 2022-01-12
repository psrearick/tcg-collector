<template>
    <div>
        <form @submit.prevent="saveCollection">
            <ui-input
                v-model="form.name"
                name="name"
                type="string"
                label="Name"
                :required="true"
                class="mb-4"
                :error-message="form.errors.name"
            />
            <ui-text-area
                v-model="form.description"
                name="description"
                type="textarea"
                label="Description"
                :required="false"
                class="mb-4"
                :error-message="form.errors.description"
            />
            <ui-checkbox
                v-model:checked="form.is_public"
                name="isPublic"
                label="Public Collection"
                :error-message="form.errors.is_public"
            />
            <div class="flex flex-row-reverse mt-4">
                <ui-button
                    text="Save"
                    button-style="primary-outline"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="updateCollection"
                />
                <action-message :on="form.recentlySuccessful" class="mr-4 py-2">
                    Saved.
                </action-message>
            </div>
        </form>
    </div>
</template>
<script>
import UiInput from "@/UI/Form/UIInput";
import UiTextArea from "@/UI/Form/UITextArea";
import UiCheckbox from "@/UI/Form/UICheckbox";
import UiButton from "@/UI/UIButton";
import ActionMessage from "@/Jetstream/ActionMessage";

export default {
    name: "UpdateCollectionDetailsForm",

    components: { UiInput, UiTextArea, UiCheckbox, UiButton, ActionMessage },

    props: {
        collection: {
            type: Object,
            default: () => {},
        },
    },

    data() {
        return {
            form: this.$inertia.form({
                _method: "PUT",
                uuid: this.collection.uuid,
                folder_uuid: this.collection.folder_uuid,
                id: this.collection.id,
                user_id: this.collection.user_id,
                name: this.collection.name,
                description: this.collection.description,
                is_public: this.collection.is_public,
            }),
        };
    },

    methods: {
        updateCollection() {
            this.form.post(
                route("collections.update", {
                    collection: this.collection.uuid,
                }),
                {
                    errorBag: "updateCollection",
                    preserveScroll: true,
                }
            );
        },
    },
};
</script>
