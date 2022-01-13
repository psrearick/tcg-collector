<template>
    <div
        v-if="$page.props.auth['admin-panel']"
        class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex"
    >
        <jet-nav-link
            :href="route('stores.index')"
            :active="route().current('stores.*')"
        >
            Stores
        </jet-nav-link>
        <jet-nav-link
            :href="route('users.index')"
            :active="route().current('users.*')"
        >
            Users
        </jet-nav-link>
    </div>
    <div v-else class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
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
