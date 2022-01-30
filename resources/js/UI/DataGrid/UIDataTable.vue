<template>
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div v-if="hasSelectMenu" class="float-right sm:mx-6 lg:mx-8">
                <ui-dropdown
                    :label="selectMenuLabel"
                    :menu="selectMenuWithItems"
                    :active="selectedOptions.length > 0"
                />
            </div>
            <div
                class="
                    py-2
                    align-middle
                    inline-block
                    min-w-full
                    sm:px-6
                    lg:px-8
                "
            >
                <div
                    class="
                        shadow
                        overflow-hidden
                        border-b border-gray-200
                        sm:rounded-lg
                    "
                >
                    <table
                        :class="
                            classes.table
                                ? classes.table
                                : 'min-w-full divide-y divide-gray-200'
                        "
                    >
                        <thead class="bg-gray-50">
                            <tr
                                :class="
                                    classes.headerRow ? classes.headerRow : ''
                                "
                            >
                                <th
                                    v-if="hasSelectMenu"
                                    class="p-2 pl-4 text-left"
                                >
                                    <ui-checkbox
                                        :checked="selectAll"
                                        @update:checked="updateSelectAll"
                                    />
                                </th>
                                <th
                                    v-if="hasExpandToggle"
                                    class="px-4 cursor-pointer"
                                >
                                    <div
                                        v-if="hasExpandToggle && expanded"
                                        @click="expand(false)"
                                    >
                                        <ui-icon icon="chevron-down-alt" />
                                    </div>
                                    <div
                                        v-if="hasExpandToggle && !expanded"
                                        @click="expand(true)"
                                    >
                                        <ui-icon icon="chevron-right" />
                                    </div>
                                </th>
                                <th
                                    v-for="(field, index) in topRowFields"
                                    :key="index"
                                    scope="col"
                                    :class="
                                        classes.headerCell
                                            ? classes.headerCell
                                            : 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'
                                    "
                                >
                                    <a
                                        href="#"
                                        class="flex"
                                        @click.prevent="sortField(field)"
                                    >
                                        <span class="block flex">
                                            <span class="whitespace-nowrap">
                                                {{
                                                    field.label
                                                        ? field.label
                                                        : ""
                                                }}
                                            </span>
                                            <span>
                                                <ui-icon
                                                    v-if="getIcon(field)"
                                                    :icon="
                                                        'sort-' + getIcon(field)
                                                    "
                                                    classes="inline ml-2"
                                                    size="1rem"
                                                    class="inline"
                                                />
                                            </span>
                                        </span>
                                        <span class="block text-gray-400">
                                            {{
                                                field.subLabel
                                                    ? field.subLabel
                                                    : ""
                                            }}
                                        </span>
                                    </a>
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            v-for="(item, key) in data"
                            :key="key"
                            :class="
                                classes.tbody
                                    ? classes.tbody
                                    : 'bg-white border-b-2 border-gray-200 hover:bg-gray-50'
                            "
                        >
                            <tr
                                :class="
                                    classes.tableRow ? classes.tableRow : ''
                                "
                            >
                                <td v-if="hasSelectMenu" class="p-2 pl-4">
                                    <ui-checkbox
                                        :checked="selectedOptions.includes(key)"
                                        @update:checked="check(key)"
                                    />
                                </td>
                                <td
                                    v-if="hasExpandToggle"
                                    class="px-4 cursor-pointer"
                                >
                                    <div
                                        v-if="isExpanded[key]"
                                        @click="expandRow(false, key)"
                                    >
                                        <ui-icon icon="chevron-down-alt" />
                                    </div>
                                    <div
                                        v-if="!isExpanded[key]"
                                        @click="expandRow(true, key)"
                                    >
                                        <ui-icon icon="chevron-right" />
                                    </div>
                                </td>
                                <td
                                    v-for="(field, fieldKey) in topRowFields"
                                    :key="fieldKey"
                                    :class="
                                        classes.tableCell
                                            ? classes.tableCell
                                            : 'py-2 px-6'
                                    "
                                >
                                    <a
                                        v-if="field.link"
                                        class="
                                            text-blue-700
                                            hover:text-blue-900
                                        "
                                        href=""
                                        @click.prevent="click(item, field)"
                                    >
                                        <ui-data-table-field
                                            :data="item"
                                            :field="field"
                                        />
                                    </a>
                                    <p v-else>
                                        <ui-data-table-field
                                            :data="item"
                                            :field="field"
                                        />
                                    </p>
                                </td>
                            </tr>
                            <tr
                                v-if="bottomRowFields.length"
                                :class="
                                    classes.tableRow ? classes.tableRow : ''
                                "
                            >
                                <td
                                    v-for="(field, fieldKey) in bottomRowFields"
                                    :key="fieldKey"
                                    :colspan="field.span ? field.span : 1"
                                    :class="
                                        classes.tableCell
                                            ? classes.tableCell
                                            : 'py-2 px-6'
                                    "
                                >
                                    <a
                                        v-if="field.link"
                                        class="
                                            text-blue-700
                                            hover:text-blue-900
                                        "
                                        href=""
                                        @click.prevent="click(item, field)"
                                    >
                                        <ui-data-table-field
                                            :data="item"
                                            :field="field"
                                        />
                                    </a>
                                    <p v-else>
                                        <ui-data-table-field
                                            :data="item"
                                            :field="field"
                                        />
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import UiDataTableField from "@/UI/DataGrid/UIDataTableField";
import UiCheckbox from "@/UI/Form/UICheckbox";
import UiDropdown from "@/UI/Dropdown/UIDropdown";
import UiIcon from "@/UI/UIIcon";

export default {
    name: "UiDataTable",

    components: { UiDataTableField, UiCheckbox, UiDropdown, UiIcon },

    props: {
        gridName: {
            type: String,
            default: "",
        },
        data: {
            type: Array,
            default: () => [],
        },
        fields: {
            type: Array,
            default: () => [],
        },
        selectMenu: {
            type: Array,
            default: () => [],
        },
        selected: {
            type: Array,
            default: () => [],
        },
        fieldRows: {
            type: Array,
            default: () => [],
        },
        classes: {
            type: Object,
            default: () => {
                return { table: null };
            },
        },
        expandedDefault: {
            type: Boolean,
            default: false,
        },
        hasExpandToggle: {
            type: Boolean,
            default: false,
        },
    },
    emits: ["expand", "expandRow"],

    data() {
        return {
            selectAll: false,
            selectedOptions: [],
            selectMenuWithItems: [],
            expanded: this.expandedDefault,
            expandedRows: [],
            collapsedRows: [],
            isExpanded: [],
        };
    },

    computed: {
        hasSelectMenu() {
            return this.selectMenu.length > 0;
        },
        selectMenuLabel() {
            return "Edit Selected (" + this.selectedOptions.length + ")";
        },
        topRowFields() {
            if (this.fields && this.fields.length) {
                return this.filterFields(this.fields);
            }
            if (this.fieldRows) {
                const topRow = this.fieldRows.filter((row) => {
                    return row.row === 1;
                });
                if (topRow.length) {
                    return this.filterFields(topRow[0].fields);
                }
            }
            return [];
        },
        bottomRowFields() {
            if (this.fieldRows) {
                const bottomRow = this.fieldRows.filter((row) => {
                    return row.row === 2;
                });
                if (bottomRow.length) {
                    return this.filterFields(bottomRow[0].fields);
                }
            }
            return [];
        },
        sortFields() {
            return this.$store.getters.sortFields;
        },
    },

    watch: {
        collapsedRows: {
            deep: true,
            handler() {
                this.checkIsExpanded();
            },
        },
        expandedRows: {
            deep: true,
            handler() {
                this.checkIsExpanded();
            },
        },
    },

    mounted() {
        this.selectedOptions = _.clone(this.selected);
        this.setMenu();
        this.checkIsExpanded();
    },

    created() {
        this.emitter.on(
            "clear_data_grid_selections",
            this.clearDataGridSelections
        );
    },

    methods: {
        checkIsExpanded() {
            this.isExpanded = this.data.map((field, key) => {
                return this.checkExpanded(key.toString());
            });
        },
        clearDataGridSelections(value) {
            if (value === this.gridName) {
                this.updateSelectAll();
            }
        },
        expand(expanded) {
            this.expanded = expanded;
            this.expandedRows = [];
            this.collapsedRows = [];
            this.$emit("expand", expanded);
        },
        expandRow(expanded, key) {
            this.expandedRows[key] = expanded;
            this.collapsedRows[key] = !expanded;
            this.$emit("expandRow", { expanded: expanded, key: key });
        },
        getIcon(field) {
            let sort = this.sortFields[this.gridName];
            if (!sort) {
                return null;
            }

            return sort[field.key] || null;
        },
        checkExpanded(key) {
            let hasMaster = this.hasExpandToggle;
            let master = this.expanded;
            let expanded =
                Object.keys(this.expandedRows).indexOf(key) > -1 &&
                this.expandedRows[key];
            let collapsed =
                Object.keys(this.collapsedRows).indexOf(key) > -1 &&
                this.collapsedRows[key];

            if (collapsed) {
                return false;
            }

            if (expanded) {
                return true;
            }

            if (hasMaster && master) {
                return master;
            }

            return false;
        },
        filterFields(fields) {
            return fields.filter((field) => {
                return field.visible;
            });
        },
        sortField(field) {
            this.$store.dispatch("addFieldToSort", {
                field: field,
                gridName: this.gridName,
            });

            this.emitter.emit("sort", this.gridName);
        },
        click(item, field) {
            this.emitter.emit(field.events.click, item);
        },
        updateSelectAll(value) {
            if (!value) {
                this.selectedOptions = [];
                this.selectAll = false;
                return;
            }

            this.selectAll = true;
            this.data.forEach((el, index) => {
                if (!this.isChecked(index)) {
                    this.check(index);
                }
            });
        },
        isChecked(key) {
            return this.selectedOptions.includes(key);
        },
        check(key) {
            const index = this.selectedOptions.indexOf(key);
            if (index > -1) {
                this.selectedOptions.splice(index, 1);
                return;
            }

            this.selectedOptions.push(key);
        },
        getData() {
            return this.data;
        },
        getSelectedOptions() {
            return this.selectedOptions;
        },
        setMenu() {
            if (!this.hasSelectMenu) {
                return;
            }

            this.selectMenuWithItems = this.selectMenu.map((item) => {
                item["selectedItems"] = this.getSelectedOptions;
                item["data"] = this.getData;
                return item;
            });
        },
    },
};
</script>
