<template>
    <div class="relative">
        <div @click="toggleOpen">
            <slot name="trigger" />
        </div>

        <!-- Full Screen Dropdown Overlay -->
        <div v-show="open" class="fixed inset-0 z-40" @click="open = false" />

        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-show="open"
                class="absolute z-50 rounded-md shadow-lg"
                :class="[widthClass, alignmentClasses, topClass]"
                style="display: none"
                @click="open = false"
            >
                <div
                    class="rounded-md ring-1 ring-black ring-opacity-5"
                    :class="contentClasses"
                >
                    <slot name="content" />
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
import { onMounted, onUnmounted, ref } from "vue";

export default {
    name: "UiDropdownMenu",

    props: {
        active: {
            type: Boolean,
            default: true,
        },
        align: {
            type: String,
            default: "right",
        },
        width: {
            type: String,
            default: "48",
        },
        contentClasses: {
            type: [Array, String],
            default: () => ["py-1", "bg-white"],
        },
        topClass: {
            type: String,
            default: "mt-4",
        },
    },

    setup() {
        let open = ref(false);

        const closeOnEscape = (e) => {
            if (open.value && e.keyCode === 27) {
                open.value = false;
            }
        };

        onMounted(() => document.addEventListener("keydown", closeOnEscape));
        onUnmounted(() =>
            document.removeEventListener("keydown", closeOnEscape)
        );

        return {
            open,
        };
    },

    computed: {
        widthClass() {
            return {
                48: "w-48",
            }[this.width.toString()];
        },

        alignmentClasses() {
            if (this.align === "left") {
                return "origin-top-left left-0";
            } else if (this.align === "right") {
                return "origin-top-right right-0";
            } else {
                return "origin-top";
            }
        },
    },

    methods: {
        toggleOpen() {
            if (!this.active) {
                this.open = false;
                return;
            }

            this.open = !this.open;
        },
    },
};
</script>
