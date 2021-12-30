<template>
    <app-layout title="Create Folder">
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
                    Create Folder
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
                                Folder Details
                            </h3>

                            <div class="sm:col-span-6">
                                <ui-input
                                    v-model="form.name"
                                    label="Name"
                                    field-id="name"
                                    name="name"
                                    type="string"
                                    placeholder="Name your folder"
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
                                    placeholder="Write a few sentences about your folder"
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
                            text="Create Folder"
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
import AppLayout from "@/Layouts/AppLayout";
import UiInput from "@/UI/Form/UIInput";
import UiButton from "@/UI/UIButton";
import UiTextArea from "@/UI/Form/UITextArea";
import UiCheckbox from "@/UI/Form/UICheckbox";

export default {
    name: "Create",

    components: { AppLayout, Link, UiInput, UiButton, UiTextArea, UiCheckbox },

    title: "MTG Collector - Create Folder",

    header: "Create Folder",

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
                folder: this.folder,
                is_public: false,
                parent_uuid: "",
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

    mounted() {
        this.form.parent_uuid = this.folder;
    },

    methods: {
        submitForm() {
            this.$inertia.post("/folders", this.form);
        },
    },
};
</script>
