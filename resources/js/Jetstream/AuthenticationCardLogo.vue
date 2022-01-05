<template>
    <Link :href="'/'">
        <ui-icon
            icon="collection"
            size="6rem"
            class="text-primary-500 hover:text-primary-700"
        />
    </Link>
</template>

<script>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import UiIcon from "../UI/UIIcon";

export default defineComponent({
    components: {
        Link,
        UiIcon,
    },
});
</script>
