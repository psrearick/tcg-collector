<template>
    <ui-panel
        :show="show"
        :form="true"
        :clear="false"
        :title="title"
        save-text="Save"
        @update:show="$emit('update:show', $event)"
        @close="closePanel"
        @save="save"
    >
        <p class="text-gray-500 text-sm py-4">{{ collection.name }}</p>
        <form>
            <ui-input
                v-model="form.name"
                name="name"
                type="string"
                label="Name"
                :required="true"
                :error-message="errorMessages.name"
                class="mb-4"
            />
            <ui-text-area
                v-model="form.description"
                name="description"
                type="textarea"
                label="Description"
                :required="false"
                :error-message="errorMessages.description"
                class="mb-4"
            />
            <ui-checkbox
                v-if="type === 'collection'"
                v-model:checked="form.is_public"
                name="isPublic"
                label="Public Collection"
            />
        </form>
    </ui-panel>
</template>

<script>
import UiPanel from "@/UI/UIPanel";
import UiInput from "@/UI/Form/UIInput";
import UiTextArea from "@/UI/Form/UITextArea";
import UiCheckbox from "@/UI/Form/UICheckbox";

export default {
    name: "EditCollectionPanel",

    components: {
        UiTextArea,
        UiInput,
        UiPanel,
        UiCheckbox,
    },

    props: {
        show: {
            type: Boolean,
            default: false,
        },
        type: {
            type: String,
            default: "collection",
        },
        collection: {
            type: Object,
            default: () => {},
        },
        errors: {
            type: Object,
            default: () => {},
        },
    },

    emits: ["update:show", "close"],

    data() {
        return {
            form: {
                name: "",
                description: "",
                id: null,
                is_public: false,
            },
            errorMessages: {},
        };
    },

    computed: {
        saveUrl: function () {
            return (
                (this.type === "collection" ? "/collections/" : "/folders/") +
                this.collection.id
            );
        },
        saveMethod: function () {
            return "patch";
        },
        title: function () {
            return (
                "Edit " + (this.type === "collection" ? "Collection" : "Folder")
            );
        },
    },

    watch: {
        errors: function (value) {
            this.errorMessages = value;
        },
        show: function (value) {
            if (value) {
                this.form = _.cloneDeep(this.collection);
                return;
            }
            this.clearForm();
        },
    },

    methods: {
        clearForm() {
            this.form = {
                name: "",
                description: "",
                id: null,
                is_public: false,
            };
            this.errorMessages = {};
        },
        close() {
            this.$emit("close");
            this.$emit("update:show", false);
        },
        closePanel() {
            this.clearForm();
            this.close();
        },
        save() {
            let self = this;
            this.$inertia.visit(this.saveUrl, {
                method: this.saveMethod,
                data: this.form,
                preserveState: true,
                onSuccess: () => {
                    self.closePanel();
                },
            });
        },
    },
};
</script>
