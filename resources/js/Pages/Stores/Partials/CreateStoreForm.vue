<template>
    <form
        class="space-y-8 divide-y divide-gray-200"
        @submit.prevent="submitForm"
    >
        <div class="space-y-8 divide-y divide-gray-200">
            <div>
                <div
                    class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6"
                >
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Store Details
                    </h3>
                    <div class="sm:col-span-6">
                        <ui-input
                            v-model="form.name"
                            label="Name"
                            field-id="name"
                            name="name"
                            type="string"
                            placeholder="Store name"
                            :error-message="form.errors.name"
                            :required="true"
                        />
                    </div>
                </div>
            </div>
        </div>
        <div class="pt-5">
            <div class="flex space-x-4 justify-end">
                <Link :href="route('stores.index')">
                    <ui-button
                        type="button"
                        button-style="white"
                        text="Cancel"
                    />
                </Link>
                <ui-button
                    type="submit"
                    button-style="primary-dark"
                    text="Create Store"
                    :disabled="form.processing"
                    @click="submitForm"
                />
            </div>
        </div>
    </form>
</template>
<script>
import UiInput from "@/UI/Form/UIInput";
import UiButton from "@/UI/UIButton";
import UiTextArea from "@/UI/Form/UITextArea";
import UiCheckbox from "@/UI/Form/UICheckbox";
import { Link } from "@inertiajs/inertia-vue3";

export default {
    name: "CreateStoreForm",

    components: { UiInput, UiButton, UiTextArea, UiCheckbox, Link },

    data() {
        return {
            form: this.$inertia.form({
                _method: "POST",
                name: "",
            }),
        };
    },

    methods: {
        submitForm() {
            this.form.post(route("stores.store"), {
                errorBag: "store-store",
                preserveScroll: true,
            });
        },
    },
};
</script>
