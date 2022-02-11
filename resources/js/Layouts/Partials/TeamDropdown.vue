<template>
    <div class="ml-3 relative">
        <!-- Teams Dropdown -->
        <jet-dropdown
            v-if="$page.props.jetstream.hasTeamFeatures"
            align="right"
            width="48"
        >
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
                        {{ $page.props.user.current_team.name }}

                        <UiIcon
                            icon="chevron-down"
                            classes="ml-2 -mr-0.5 h-4 w-4"
                        />
                    </button>
                </span>
            </template>

            <template #content>
                <div>
                    <!-- Team Management -->
                    <template v-if="$page.props.jetstream.hasTeamFeatures">
                        <div class="block px-4 py-2 text-xs text-gray-400">
                            Manage Group
                        </div>

                        <!-- Team Settings -->
                        <jet-dropdown-link
                            :href="
                                route(
                                    'teams.show',
                                    $page.props.user.current_team
                                )
                            "
                        >
                            Group Settings
                        </jet-dropdown-link>

                        <jet-dropdown-link
                            v-if="$page.props.jetstream.canCreateTeams"
                            :href="route('teams.create')"
                        >
                            Create New Group
                        </jet-dropdown-link>

                        <div class="border-t border-gray-100"></div>

                        <!-- Team Switcher -->
                        <div class="block px-4 py-2 text-xs text-gray-400">
                            Switch Groups
                        </div>

                        <template
                            v-for="team in $page.props.user.all_teams"
                            :key="team.id"
                        >
                            <form @submit.prevent="switchToTeam(team)">
                                <jet-dropdown-link as="button">
                                    <div class="flex items-center">
                                        <svg
                                            v-if="
                                                team.id ==
                                                $page.props.user.current_team_id
                                            "
                                            class="mr-2 h-5 w-5 text-green-400"
                                            fill="none"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                            ></path>
                                        </svg>
                                        <div>
                                            {{ team.name }}
                                        </div>
                                    </div>
                                </jet-dropdown-link>
                            </form>
                        </template>
                    </template>
                </div>
            </template>
        </jet-dropdown>
    </div>
</template>
<script>
import JetDropdown from "@/Jetstream/Dropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink.vue";
import UiIcon from "@/UI/UIIcon";

export default {
    name: "TeamDropdown",

    components: {
        UiIcon,
        JetDropdown,
        JetDropdownLink,
    },

    methods: {
        switchToTeam(team) {
            this.$inertia.put(
                route("current-team.update"),
                {
                    team_id: team.id,
                },
                {
                    preserveState: false,
                }
            );
        },
    },
};
</script>
