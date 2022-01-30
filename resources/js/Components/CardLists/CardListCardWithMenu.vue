<template>
    <div :class="getStatusClass('')">
        <div
            class="
                relative
                rounded-lg
                border
                px-3
                py-2
                shadow-md
                text-center
                h-full
                border-gray-300
                bg-white
                hover:border-gray-300
                hover:bg-gray-100
            "
        >
            <div :class="'grid grid-cols-8 ' + gridClasses">
                <div class="col-span-1">
                    <slot name="left"></slot>
                </div>
                <div :class="'col-span-6 ' + mainClasses">
                    <Link
                        v-if="hasLink"
                        :href="href"
                        class="focus:outline-none"
                    >
                        <slot name="main"></slot>
                    </Link>
                    <div v-else>
                        <span
                            class="absolute inset-0"
                            aria-hidden="true"
                        ></span>
                        <slot name="main"></slot>
                    </div>
                </div>
                <div class="col-span-1 flex flex-col">
                    <div v-if="menu">
                        <div>
                            <ui-dropdown-menu top-class="mt-8">
                                <template #trigger>
                                    <ui-icon
                                        icon="dots-vertical"
                                        size="20px"
                                        class="
                                            float-right
                                            m-1
                                            hover:text-indigo-500
                                        "
                                    />
                                </template>
                                <template #content>
                                    <ui-dropdown-link
                                        v-for="(menuItem, menuIndex) in menu"
                                        :key="menuIndex"
                                        :click="menuItem"
                                    >
                                        {{ menuItem.content }}
                                    </ui-dropdown-link>
                                </template>
                            </ui-dropdown-menu>
                        </div>
                    </div>
                    <slot name="right"></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import UiDropdownMenu from "../../UI/Dropdown/UIDropdownMenu.vue";
import UiIcon from "../../UI/UIIcon";
import UiDropdownLink from "../../UI/Dropdown/UIDropdownLink.vue";

export default {
    name: "CardListCardWithMenu",

    components: { UiDropdownMenu, UiIcon, UiDropdownLink, Link },

    props: {
        gridClasses: {
            type: String,
            default: "",
        },
        href: {
            type: String,
            default: "",
        },
        mainClasses: {
            type: String,
            default: "",
        },
        status: {
            type: String,
            default: "",
        },
        menu: {
            type: Array,
            default: () => {},
        },
    },
    computed: {
        hasLink() {
            return this.href.length > 0;
        },
    },

    methods: {
        getStatusClass(currentClasses) {
            if (this.status === "") {
                return currentClasses;
            }

            let classes = [currentClasses, "pt-2 rounded-lg"];

            let hoverClasses = [""];

            if (this.status === "primary") {
                classes.push("border-primary-700 bg-primary-500");
                hoverClasses.push(
                    "hover:border-primary-700 hover:bg-primary-700"
                );
            } else if (this.status === "danger") {
                classes.push("border-red-300 bg-red-100");
                hoverClasses.push("hover:border-red-400 hover:bg-red-200");
            } else if (this.status === "success") {
                classes.push("border-green-700 bg-green-500");
                hoverClasses.push("hover:border-green-700 hover:bg-green-700");
            } else if (this.status === "warning") {
                classes.push("border-yellow-300 bg-yellow-100");
                hoverClasses.push(
                    "hover:border-yellow-400 hover:bg-yellow-200"
                );
            } else {
                classes.push("");
                hoverClasses.push("");
            }

            let classString = classes.join(" ");

            if (this.hasLink) {
                classString += " " + hoverClasses.join(" ");
            }

            return classString;
        },
    },
};
</script>
