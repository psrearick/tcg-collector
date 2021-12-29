<template>
    <ui-icon icon="collection" :size="size" :classes="classes" />
</template>

<script>
import UiIcon from "@/UI/UIIcon";
export default {
    name: "ApplicationMark",
    components: { UiIcon },
    props: {
        size: {
            type: String,
            default: null,
        },
        classes: {
            type: String,
            default: "",
        },
    },
};
</script>
