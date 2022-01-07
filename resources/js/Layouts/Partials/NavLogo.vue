<template>
    <div class="shrink-0 flex items-center">
        <Link :href="route('dashboard')">
            <jet-application-mark
                class="block h-9 w-auto text-primary-500 hover:text-primary-700"
            />
        </Link>
    </div>
</template>
<script>
import JetApplicationMark from "@/Jetstream/ApplicationMark.vue";
import { Link } from "@inertiajs/inertia-vue3";

export default {
    name: "NavLogo",

    components: { Link, JetApplicationMark },
};
</script>
