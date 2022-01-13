<template>
    <div class="ml-3 relative">
        <jet-dropdown align="right" width="48">
            <template #trigger>
                <button
                    v-if="$page.props.jetstream.managesProfilePhotos"
                    class="
                        flex
                        text-sm
                        border-2 border-transparent
                        rounded-full
                        focus:outline-none
                        focus:border-gray-300
                        transition
                    "
                >
                    <img
                        class="h-8 w-8 rounded-full object-cover"
                        :src="$page.props.user.profile_photo_url"
                        :alt="$page.props.user.name"
                    />
                </button>

                <span v-else class="inline-flex rounded-md">
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
                            hover:text-gray-700
                            focus:outline-none
                            transition
                        "
                    >
                        {{ $page.props.user.name }}

                        <svg
                            class="ml-2 -mr-0.5 h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>
                </span>
            </template>

            <template #content>
                <!-- Admin Panel -->
                <jet-dropdown-link :href="route('admin-panel.edit')">
                    {{ $page.props.auth["admin-panel"] ? "Leave" : "" }} Admin
                    Panel
                </jet-dropdown-link>

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

export default {
    name: "SettingsDropdown",

    components: {
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
