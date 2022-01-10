<template>
    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
        <jet-nav-link
            :href="route('collections-search.show')"
            :active="route().current('collections-search.show')"
        >
            Search
        </jet-nav-link>
        <jet-nav-link
            :href="route('collections.index')"
            :active="
                route().current('collections.*') || route().current('folders.*')
            "
        >
            Collections
        </jet-nav-link>
        <jet-nav-link
            :href="route('groups.index')"
            :active="route().current('groups.index')"
        >
            Group
        </jet-nav-link>
    </div>
</template>
<script>
import JetNavLink from "@/Jetstream/NavLink";

export default {
    name: "PrimaryNavigationLinks",

    components: { JetNavLink },
};
</script>
