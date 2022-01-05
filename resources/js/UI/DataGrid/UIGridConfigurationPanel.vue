<template>
    <ui-panel
        :show="show"
        :form="true"
        :clear="false"
        title="Configure Table"
        save-text="Save"
        @update:show="$emit('update:show', $event)"
        @close="closePanel"
        @save="save"
    >
        <p class="text-gray-500 text-sm my-4 font-bold">Sort Fields</p>
        <div>
            <div
                v-for="(field, index) in sortableFields"
                :key="index"
                class="border border-1 rounded p-2 mb-2 shadow grid grid-cols-2"
            >
                <span class="whitespace-nowrap">
                    {{ field.label }}
                </span>
                <div class="grid grid-cols-3">
                    <span @click="updateSort(field)">
                        <ui-icon
                            v-if="field.sortDirection"
                            :icon="'sort-' + field.sortDirection"
                            classes="inline hover:text-gray-500"
                            size="1rem"
                            class="inline"
                        />
                        <ui-icon
                            v-else
                            icon="circle-x"
                            classes="inline hover:text-gray-500"
                            size="1rem"
                            class="inline"
                        />
                    </span>
                    <span>
                        <ui-icon
                            v-if="field.sortOrder > 0"
                            icon="arrow-narrow-up"
                            classes="inline hover:text-gray-500"
                            size="1rem"
                            class="inline"
                            @click="moveUp(field)"
                        />
                    </span>
                    <span>
                        <ui-icon
                            v-if="field.sortOrder < sortableFields.length - 1"
                            icon="arrow-narrow-down"
                            classes="inline hover:text-gray-500"
                            size="1rem"
                            class="inline"
                            @click="moveDown(field)"
                        />
                    </span>
                </div>
            </div>
        </div>
        <div v-if="filterableFields">
            <p class="text-gray-500 text-sm mt-8 mb-4 font-bold">
                Filter Fields
            </p>
            <div>
                <form>
                    <div
                        v-for="(field, index) in filterableFields"
                        :key="index"
                    >
                        <component
                            :is="field.uiComponent"
                            v-if="filters[field.key]"
                            v-model="filters[field.key]"
                            :field="field"
                        />
                    </div>
                </form>
            </div>
        </div>
    </ui-panel>
</template>

<script>
import UiIcon from "@/UI/UIIcon";
import UiButton from "@/UI/UIButton";
import UiInput from "@/UI/Form/UIInput";
import UiMinMax from "@/UI/Form/UIMinMax";
import UiPanel from "@/UI/UIPanel";
import UiTextArea from "@/UI/Form/UITextArea";
import sortConfigurationFields from "@/UI/Composables/sortConfigurationFields";
import filterConfigurationFields from "@/UI/Composables/filterConfigurationFields";
import { toRefs } from "vue";

export default {
    name: "UiGridConfigurationPanel",

    components: {
        UiIcon,
        UiButton,
        UiInput,
        UiMinMax,
        UiPanel,
        UiTextArea,
    },

    props: {
        fields: {
            type: Array,
            default: () => [],
        },
        gridName: {
            type: String,
            default: "",
        },
        show: {
            type: Boolean,
            default: false,
        },
    },

    emits: ["update:show", "close"],

    setup(props) {
        const { fields, gridName } = toRefs(props);

        const {
            sortFields,
            sortOrder,
            sortableFields,
            getCurrentSortFields,
            getCurrentSortOrder,
            updateSort,
            moveUp,
            moveDown,
        } = sortConfigurationFields(fields, gridName);

        const { filterableFields, getCurrentFilters, filters } =
            filterConfigurationFields(fields, gridName);

        return {
            filterableFields,
            getCurrentFilters,
            filters,
            sortFields,
            sortOrder,
            sortableFields,
            getCurrentSortFields,
            getCurrentSortOrder,
            updateSort,
            moveUp,
            moveDown,
        };
    },

    watch: {
        show: function (value) {
            if (value) {
                this.getCurrentSortFields();
                this.getCurrentSortOrder();
                this.getCurrentFilters();
                return;
            }
            this.clearForm();
        },
    },

    methods: {
        clearForm() {
            this.filters = {};
            this.sortFields = {};
            this.sortOrder = {};
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
            this.$store.dispatch("setSortOrder", {
                order: this.sortOrder,
                gridName: this.gridName,
            });
            this.$store.dispatch("setSortFields", {
                fields: this.sortFields,
                gridName: this.gridName,
            });
            this.$store.dispatch("setFilters", {
                filters: this.filters,
                gridName: this.gridName,
            });
            this.emitter.emit("sort", this.gridName);
            this.closePanel();
        },
    },
};
</script>
