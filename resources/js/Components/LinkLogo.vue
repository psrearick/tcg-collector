<template>
    <Link :href="href">
        <application-mark classes="w-9 h-9" />
    </Link>
</template>

<script>
import ApplicationMark from "@/Components/ApplicationMark";
import { Link } from "@inertiajs/inertia-vue3";

export default {
    name: "LinkLogo",
    components: { ApplicationMark, Link },
    props: {
        href: {
            type: String,
            default: "/",
        },
    },
};
</script>
