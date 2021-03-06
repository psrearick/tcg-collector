<template>
    <div class="w-full">
        <div class="w-full flex">
            <div :class="containerClasses">
                <div v-if="cardSearch" :class="cardContainerClasses">
                    <label class="ml-3 text-xs text-gray-500" for="searchCard">
                        Card
                    </label>
                    <div class="relative">
                        <div :class="iconClass">
                            <ui-icon icon="search" />
                        </div>
                        <input
                            id="searchCard"
                            name="searchCard"
                            :class="cardFieldClasses"
                            placeholder="Search by Card Name"
                            type="search"
                            :value="modelValue"
                            @input="searchCards"
                        />
                    </div>
                </div>
                <div v-if="setSearch">
                    <label for="searchSet" class="ml-3 text-xs text-gray-500"
                        >Set</label
                    >
                    <div class="relative">
                        <div :class="iconClass">
                            <ui-icon icon="search" />
                        </div>
                        <input
                            id="searchSet"
                            name="searchSet"
                            :class="setFieldClasses"
                            placeholder="Search by Set Name or Code"
                            type="search"
                            :value="setName"
                            @input="searchSets"
                        />
                    </div>
                </div>
            </div>
            <div v-if="configureTable" class="flex flex-col-reverse">
                <ui-button
                    class="ml-2"
                    @click="$emit('gridConfigurationClick')"
                >
                    <ui-icon icon="cog" size="1.4rem" />
                </ui-button>
            </div>
        </div>
        <p v-if="markSearching && searching" class="text-xs text-gray-400">
            Searching...
        </p>
    </div>
</template>

<script>
import UiIcon from "@/UI/UIIcon";
import UiButton from "@/UI/UIButton.vue";

export default {
    name: "CardSetSearch",

    components: { UiIcon, UiButton },

    props: {
        cardSearch: {
            type: Boolean,
            default: true,
        },
        configureTable: {
            type: Boolean,
            default: true,
        },
        setName: {
            type: String,
            default: "",
        },
        setSearch: {
            type: Boolean,
            default: true,
        },
        modelValue: {
            type: String,
            default: "",
        },
        searching: {
            type: Boolean,
            default: false,
        },
        markSearching: {
            type: Boolean,
            default: false,
        },
    },

    emits: [
        "update:setName",
        "update:modelValue",
        "update:searching",
        "gridConfigurationClick",
    ],

    data() {
        return {
            fieldClasses:
                "block w-full pl-10 pr-3 py-2 border rounded-md leading-5 sm:text-sm placeholder-gray-400 focus:outline-none focus:ring-white focus:text-gray-900 bg-white border-gray-300 text-gray-700 focus:bg-gray-50 focus:border-gray-500",
            iconClass:
                "absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none",
        };
    },

    computed: {
        both() {
            return this.cardSearch && this.setSearch;
        },
        containerClasses() {
            let classes = "flex-1 grid grid-cols-1";
            if (this.both) {
                classes += " md:grid-cols-3 lg:grid-cols-4";
            }
            return classes;
        },
        cardContainerClasses() {
            let classes = "col-span-1";
            if (this.both) {
                classes += " md:col-span-2 lg:col-span-3";
            }
            return classes;
        },
        cardFieldClasses() {
            const cardClass = this.both
                ? " md:rounded-l-md md:rounded-r-none"
                : " md:rounded-l-md md:rounded-r-md";
            return this.fieldClasses + cardClass;
        },
        setFieldClasses() {
            const setClass = this.both
                ? " md:rounded-r-md md:rounded-l-none"
                : " md:rounded-l-md md:rounded-r-md";
            return this.fieldClasses + setClass;
        },
    },

    methods: {
        searchCards($event) {
            this.$emit("update:searching", true);
            this.$emit("update:modelValue", $event.target.value);
        },
        searchSets($event) {
            this.$emit("update:searching", true);
            this.$emit("update:setName", $event.target.value);
        },
    },
};
</script>
