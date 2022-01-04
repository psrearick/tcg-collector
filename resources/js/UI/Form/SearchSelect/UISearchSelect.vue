<template>
    <div>
        <label
            id="listbox-label"
            class="block text-sm font-medium text-gray-700"
        >
            {{ label }}
        </label>
        <div class="mt-1 relative">
            <ui-search-select-field
                :active="active"
                :selected-option="selectedOption"
                :model-value="searchTerm"
                @update-active="updateActive"
                @update:model-value="search"
            />
            <p v-if="searching" class="text-xs text-gray-400">Searching...</p>
            <transition
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <ui-search-select-options v-if="active && options.length">
                    <ui-search-select-option
                        v-for="(option, index) in selectOptions"
                        :key="index"
                        :selected="index === selectedOptionIndex"
                        :option="option"
                        @selectOption="updatedSelectedOption(index)"
                    />
                </ui-search-select-options>
            </transition>
        </div>
    </div>
</template>

<script>
import UiSearchSelectOptions from "@/UI/Form/SearchSelect/UISearchSelectOptions";
import UiSearchSelectOption from "@/UI/Form/SearchSelect/UISearchSelectOption";
import UiSearchSelectField from "@/UI/Form/SearchSelect/UISearchSelectField";
export default {
    name: "UiSearchSelect",

    components: {
        UiSearchSelectField,
        UiSearchSelectOption,
        UiSearchSelectOptions,
    },

    props: {
        label: {
            type: String,
            default: "",
        },
        options: {
            type: Array,
            default: () => {},
        },
        selected: {
            type: Number,
            default: -1,
        },
        searchTerm: {
            type: String,
            default: "",
        },
        searching: {
            type: Boolean,
            default: false,
        },
    },

    emits: ["update:searchTerm", "update:selectedOption"],

    data() {
        return {
            active: false,
            selectedOptionIndex: null,
        };
    },

    computed: {
        selectedOption() {
            if (
                this.selectedOptionIndex !== null &&
                typeof this.selectedOptionIndex !== "undefined"
            ) {
                return this.selectOptions[this.selectedOptionIndex];
            }
            return {};
        },
        selectOptions() {
            const options = _.cloneDeep(this.options);
            options.unshift({
                primary: "Please select an option",
                secondary: "",
                id: -1,
            });
            return options;
        },
    },

    watch: {
        selected() {
            if (
                this.selected === null ||
                typeof this.selected === "undefined"
            ) {
                this.selectedOptionIndex = null;
            }
        },
    },

    mounted() {
        if (this.selected !== null && typeof this.selected !== "undefined") {
            this.selectedOptionIndex = this.selected + 1;
        }
    },

    methods: {
        updateActive(val) {
            this.active = val;
        },
        updatedSelectedOption(index) {
            this.selectedOptionIndex = index;
            this.$emit("update:selectedOption", {
                id: this.selectedOption.id,
                index: index,
            });
            this.active = false;
        },
        search(term) {
            this.$emit("update:searchTerm", term);
        },
    },
};
</script>
