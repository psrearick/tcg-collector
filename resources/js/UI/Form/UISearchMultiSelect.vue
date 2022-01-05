<template>
    <div>
        <div class="relative inline-block text-left w-full z-50">
            <ui-input-label :name="name" :label="label" :required="required" />
            <div>
                <button
                    id="menu-button"
                    ref="visibilityToggle"
                    type="button"
                    :class="inputClass"
                    aria-expanded="true"
                    aria-haspopup="true"
                    @click="toggleShow"
                >
                    <span class="flex w-full">
                        <span class="pl-2 flex items-center gap-x-2">
                            <span
                                v-for="(selection, index) in selected"
                                :key="index"
                                class="
                                    flex
                                    items-center
                                    bg-gray-100
                                    py-2
                                    rounded-lg
                                "
                            >
                                <span @click="deselect(selection)">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 mx-2 hover:text-gray-500"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </span>
                                <span class="text-xs mr-3">
                                    {{ getSelectionDisplay(selection) }}
                                </span>
                            </span>
                        </span>
                        <input
                            type="text"
                            class="
                                flex-1
                                h-full
                                w-full
                                rounded-md
                                border-0
                                ring-0
                                focus:border-0
                                focus:ring-0
                            "
                            :value="value"
                            @focus="inputFocus"
                            @blur="focus = false"
                            @input="
                                $emit('update:modelValue', $event.target.value)
                            "
                        />
                    </span>
                </button>
            </div>

            <transition
                enter-active-class="transition ease-out duration-100"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
            >
                <ul
                    v-show="showDropdown"
                    v-closable="{
                        exclude: ['visibilityToggle'],
                        handler: 'closeShow',
                    }"
                    class="
                        origin-top-right
                        absolute
                        right-0
                        mt-2
                        w-full
                        rounded-md
                        shadow-lg
                        bg-white
                        ring-1 ring-black ring-opacity-5
                        divide-y divide-gray-200
                        focus:outline-none
                    "
                    role="menu"
                    aria-orientation="vertical"
                    aria-labelledby="menu-button"
                    tabindex="-1"
                >
                    <li
                        v-for="(option, index) in data"
                        :id="name + '-' + index"
                        :key="index"
                        :class="liClass(index, option[keyName])"
                        role="option"
                        @mouseenter="mouseOn(index)"
                        @mouseleave="mouseOff()"
                        @click="select(option[keyName])"
                    >
                        <span
                            :class="
                                (isSelected(option[keyName])
                                    ? 'font-semibold'
                                    : 'font-normal') + ' block truncate'
                            "
                        >
                            {{ option[display] }}
                        </span>
                        <span
                            v-if="isSelected(option[keyName])"
                            :class="checkmarkClass(index)"
                        >
                            <svg
                                class="h-5 w-5"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </span>
                    </li>
                </ul>
            </transition>
        </div>
    </div>
</template>

<script>
import UiInputLabel from "@/UI/Form/UIInputLabel";
export default {
    name: "UiSearchMultiSelect",
    components: { UiInputLabel },
    props: {
        data: {
            type: Object,
            default: () => {},
        },
        current: {
            type: Object,
            default: () => {},
        },
        display: {
            type: String,
            default: "",
        },
        keyName: {
            type: String,
            default: "",
        },
        label: {
            type: String,
            default: "",
        },
        name: {
            type: String,
            default: "",
        },
        required: {
            type: Boolean,
            default: false,
        },
        selected: {
            type: Array,
            default: () => {},
        },
        show: {
            type: Boolean,
            default: false,
        },
        value: {
            type: String,
            default: "",
        },
    },

    emits: ["focus", "update:modelValue", "update:show", "select", "deselect"],

    data: function () {
        return {
            focus: false,
            mouse: null,
        };
    },

    computed: {
        inputClass() {
            let inputClass = `
            inline-flex
            justify-center
            w-full
            rounded-md
            border
            border-gray-300
            shadow-sm
            bg-white
            text-sm
            font-medium
            text-gray-700
            `;
            let focusClass = "ring-0";
            if (this.focus) {
                focusClass = "outline-none ring-2 ring-primary-500";
            }
            return `${inputClass} ${focusClass}`;
        },
        showDropdown() {
            return this.show && Object.keys(this.data).length;
        },
    },

    methods: {
        checkmarkClass(index) {
            return (
                (this.mouse === index ? "text-primary-300" : "text-white") +
                " absolute inset-y-0 right-0 flex items-center pr-4"
            );
        },
        deselect(option) {
            this.$emit("deselect", option);
        },
        inputFocus() {
            this.focus = true;
            this.$emit("focus");
        },
        isSelected(option) {
            return this.selected.findIndex((elem) => elem === option) > -1;
        },
        getSelectionDisplay(selection) {
            let index = this.data.findIndex((elem) => elem.id === selection);
            if (index === -1) {
                index = this.current.findIndex((elem) => elem.id === selection);
                return this.current[index][this.display];
            }

            return this.data[index][this.display];
        },
        liClass(index, option) {
            return (
                (this.mouse === index || this.isSelected(option)
                    ? "text-white bg-primary-600"
                    : "text-gray-900") +
                " cursor-default select-none relative py-2 pl-3 pr-9 first:rounded-t last:rounded-b"
            );
        },
        mouseOff() {
            this.mouse = null;
        },
        mouseOn(index) {
            this.mouse = index;
        },
        select(option) {
            this.toggleShow();
            if (this.isSelected(option)) {
                return this.deselect(option);
            }

            this.$emit("select", option);
        },
        closeShow() {
            this.$emit("update:show", false);
        },
        toggleShow() {
            let show = this.focus ? true : !this.show;
            this.$emit("update:show", show);
        },
    },
};
</script>
