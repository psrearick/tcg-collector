<template>
    <div>
        <nav class="bg-white border-b border-gray-100">
            <primary-navigation />
            <responsive-navigation />
        </nav>
    </div>
</template>
<script>
import PrimaryNavigation from "@/Layouts/Partials/PrimaryNavigation";
import ResponsiveNavigation from "@/Layouts/Partials/ResponsiveNavigation";

export default {
    name: "Navigation",

    components: { PrimaryNavigation, ResponsiveNavigation },
};
</script>
