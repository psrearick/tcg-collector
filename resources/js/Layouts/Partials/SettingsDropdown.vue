<template>
    <div class="ml-3 relative">
        <jet-dropdown align="right" width="48">
            <template #trigger>
                <span class="inline-flex rounded-md">
                    <button
                        type="button"
                        class="
                            inline-flex
                            items-center
                            px-3
                            py-2
                            border border-transparent
                            text-sm
                            leading-4
                            font-medium
                            rounded-md
                            text-gray-500
                            bg-white
                            hover:bg-gray-100
                            hover:text-gray-700
                            focus:outline-none
                            focus:bg-gray-100
                            active:bg-gray-100
                            transition
                        "
                    >
                        {{ $page.props.user.name }}

                        <UiIcon
                            icon="chevron-down"
                            classes="ml-2 -mr-0.5 h-4 w-4"
                        />
                    </button>
                </span>
            </template>

            <template #content>
                <!-- Admin Panel -->
                <!-- <jet-dropdown-link :href="route('admin-panel.edit')">
                    {{ $page.props.auth["admin-panel"] ? "Leave" : "" }} Admin
                    Panel
                </jet-dropdown-link> -->

                <!-- Account Management -->
                <div class="block px-4 py-2 text-xs text-gray-400">
                    Manage Account
                </div>

                <jet-dropdown-link :href="route('settings.show')">
                    Settings
                </jet-dropdown-link>

                <jet-dropdown-link :href="route('profile.show')">
                    Profile
                </jet-dropdown-link>

                <jet-dropdown-link
                    v-if="$page.props.jetstream.hasApiFeatures"
                    :href="route('api-tokens.index')"
                >
                    API Tokens
                </jet-dropdown-link>

                <div class="border-t border-gray-100"></div>

                <!-- Authentication -->
                <form @submit.prevent="logout">
                    <jet-dropdown-link as="button"> Log Out </jet-dropdown-link>
                </form>
            </template>
        </jet-dropdown>
    </div>
</template>
<script>
import JetDropdown from "@/Jetstream/Dropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink.vue";
import UiIcon from "@/UI/UIIcon";

export default {
    name: "SettingsDropdown",

    components: {
        UiIcon,
        JetDropdown,
        JetDropdownLink,
    },

    methods: {
        logout() {
            this.$inertia.post(route("logout"));
        },
    },
};
</script>
