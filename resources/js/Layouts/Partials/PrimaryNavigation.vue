<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <nav-logo />
                <primary-navigation-links />
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <team-dropdown />
                <settings-dropdown />
            </div>

            <responsive-navigation-trigger />
        </div>
    </div>
</template>
<script>
import NavLogo from "@/Layouts/Partials/NavLogo";
import PrimaryNavigationLinks from "@/Layouts/Partials/PrimaryNavigationLinks";
import TeamDropdown from "@/Layouts/Partials/TeamDropdown";
import SettingsDropdown from "@/Layouts/Partials/SettingsDropdown";
import ResponsiveNavigationTrigger from "@/Layouts/Partials/ResponsiveNavigationTrigger";

export default {
    name: "PrimaryNavigation",

    components: {
        NavLogo,
        PrimaryNavigationLinks,
        TeamDropdown,
        SettingsDropdown,
        ResponsiveNavigationTrigger,
    },
};
</script>
