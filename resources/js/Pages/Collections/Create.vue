<template>
    <app-layout title="Create Collection">
        <template #header>
            <div>
                <h2
                    class="
                        font-semibold
                        text-xl text-gray-800
                        leading-tight
                        py-2
                    "
                >
                    Create Collection
                </h2>
            </div>
        </template>
        <div>
            <form
                class="space-y-8 divide-y divide-gray-200"
                @submit.prevent="submitForm"
            >
                <div class="space-y-8 divide-y divide-gray-200">
                    <div>
                        <div
                            class="
                                mt-6
                                grid grid-cols-1
                                gap-y-6 gap-x-4
                                sm:grid-cols-6
                            "
                        >
                            <h3
                                class="
                                    text-lg
                                    leading-6
                                    font-medium
                                    text-gray-900
                                "
                            >
                                Collection Details
                            </h3>
                            <div class="sm:col-span-6">
                                <ui-input
                                    v-model="form.name"
                                    label="Name"
                                    field-id="name"
                                    name="name"
                                    type="string"
                                    placeholder="Name your collection"
                                    :required="true"
                                />
                            </div>

                            <div class="sm:col-span-6">
                                <ui-text-area
                                    v-model="form.description"
                                    name="description"
                                    type="textarea"
                                    label="Description"
                                    :required="false"
                                    placeholder="Write a few sentences about your collection"
                                    class="mb-4"
                                />
                            </div>

                            <div class="sm:col-span-6">
                                <ui-checkbox
                                    v-model:checked="form.is_public"
                                    name="is_public"
                                    label="Public Collection"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pt-5">
                    <div class="flex space-x-4 justify-end">
                        <Link :href="cancelLink">
                            <ui-button
                                type="button"
                                button-style="white"
                                text="Cancel"
                            />
                        </Link>
                        <ui-button
                            type="button"
                            button-style="primary-dark"
                            text="Create Collection"
                            @click="submitForm"
                        />
                    </div>
                </div>
            </form>
        </div>
    </app-layout>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import UiInput from "@/UI/Form/UIInput";
import UiButton from "@/UI/UIButton";
import UiTextArea from "@/UI/Form/UITextArea";
import UiCheckbox from "@/UI/Form/UICheckbox";

export default {
    name: "Create",

    components: { UiInput, UiButton, UiTextArea, UiCheckbox, Link, AppLayout },

    title: "MTG Collector - Create Collection",

    header: "Create Collection",

    props: {
        folder: {
            type: String,
            default: null,
        },
    },

    data() {
        return {
            form: {
                name: "",
                description: "",
                folder_uuid: this.folder,
                is_public: false,
            },
        };
    },

    computed: {
        cancelLink() {
            if (this.folder) {
                return route("folders.show", {
                    folder: this.folder,
                });
            }

            return route("collections.index");
        },
    },

    methods: {
        submitForm() {
            this.$inertia.post("/collections", this.form);
        },
    },
};
</script>
