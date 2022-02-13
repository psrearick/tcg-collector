<template>
    <div :class="classes">
        <slot />
    </div>
</template>

<script>
export default {
    name: "UiCard",

    props: {
        mode: {
            type: String,
            default: "default",
        },
        cardStyle: {
            type: String,
            default: "default",
        },
        padding: {
            type: String,
            default: "p-8",
        },
        active: {
            type: Boolean,
            default: false,
        },
    },

    computed: {
        classes() {
            let classes = [`rounded-lg border ${this.padding}`];

            if (this.cardStyle === "default" && this.mode !== "danger") {
                classes.push("bg-gray-200");
            }

            if (this.cardStyle === "white") {
                classes.push("bg-white");
            }

            if (this.active) {
                classes.push("border-transparent ring-2");
            } else {
                classes.push("border-1 ring-0");
            }

            if (this.mode === "default") {
                classes.push(
                    "border-gray-300 hover:border-gray-500 ring-gray-500"
                );
            }

            if (this.mode === "danger") {
                classes.push("bg-red-100 border-red-500 ring-red-500");
            }

            if (this.mode === "primary") {
                classes.push("border-primary-500 ring-primary-500");
            }

            return classes.join(" ");
        },
    },
};
</script>
