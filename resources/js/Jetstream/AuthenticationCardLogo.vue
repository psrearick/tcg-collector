<template>
    <Link :href="'/'">
        <ui-icon icon="collection" size="6rem" />
    </Link>
</template>

<script>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import UiIcon from "../UI/UIIcon";

export default defineComponent({
    components: {
        Link,
        UiIcon
    },
});
</script>
