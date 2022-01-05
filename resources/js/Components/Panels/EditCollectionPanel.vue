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
                class="mb-4"
            />
            <ui-search-multi-select
                v-model:show="groupsShow"
                :value="groupsValue"
                :data="groupsData"
                :selected="form.groups"
                :current="currentGroups"
                label="Groups"
                name="groups"
                key-name="id"
                display="name"
                class="mb-4"
                @update:model-value="searchGroups"
                @focus="groupsFocus"
                @select="selectGroup"
                @deselect="deselectGroup"
            />
        </form>
    </ui-panel>
</template>

<script>
import UiPanel from "@/UI/UIPanel";
import UiInput from "@/UI/Form/UIInput";
import UiTextArea from "@/UI/Form/UITextArea";
import UiCheckbox from "@/UI/Form/UICheckbox";
import UiSearchMultiSelect from "@/UI/Form/UISearchMultiSelect";

export default {
    name: "EditCollectionPanel",

    components: {
        UiTextArea,
        UiInput,
        UiPanel,
        UiCheckbox,
        UiSearchMultiSelect,
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
                groups: null,
            },
            errorMessages: {},
            groupsShow: false,
            groupsValue: "",
            groupsData: [],
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
        currentGroups: function () {
            if (!this.form.groups) {
                return [];
            }

            let groups = [];
            if (this.$page.props.auth.user.owned_teams.length) {
                this.$page.props.auth.user.owned_teams.forEach((team) => {
                    groups.push(team);
                });
            }
            if (this.$page.props.auth.user.teams.length) {
                this.$page.props.auth.user.teams.forEach((team) => {
                    groups.push(team);
                });
            }
            return groups.filter((group) => {
                return (
                    this.form.groups.findIndex(
                        (formGroup) => formGroup === group.id
                    ) > -1
                );
            });
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
                groups: null,
            };
            this.errorMessages = {};
            this.groupsShow = false;
            this.groupsValue = "";
            this.groupsData = [];
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
        groupsFocus() {
            let groups = [];
            if (this.$page.props.auth.user.owned_teams.length) {
                this.$page.props.auth.user.owned_teams.forEach((team) => {
                    groups.push(team);
                });
            }
            if (this.$page.props.auth.user.teams.length) {
                this.$page.props.auth.user.teams.forEach((team) => {
                    groups.push(team);
                });
            }

            this.groupsData = groups;
        },
        searchGroups(value) {
            let groups = [];
            if (this.$page.props.auth.user.owned_teams.length) {
                this.$page.props.auth.user.owned_teams.forEach((team) => {
                    groups.push(team);
                });
            }
            if (this.$page.props.auth.user.teams.length) {
                this.$page.props.auth.user.teams.forEach((team) => {
                    groups.push(team);
                });
            }

            this.groupsData = groups.filter((group) => {
                group.name.indexOf(value) > -1;
            });
        },
        selectGroup(option) {
            if (this.form.groups.indexOf(option) === -1) {
                this.form.groups.push(option);
            }

            this.groupsValue = "";
        },
        deselectGroup(option) {
            let formIndex = this.form.groups.findIndex(
                (elem) => elem === option
            );
            this.form.groups.splice(formIndex, 1);
        },
    },
};
</script>
