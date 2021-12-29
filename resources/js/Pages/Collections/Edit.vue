<template>
    <app-layout title="Edit Collection">
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Edit Collection
                </h2>
                <div>Buttons</div>
            </div>
        </template>
    </app-layout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
    name: "Edit",

    components: { AppLayout },

    props: {
        collection: {
            type: Object,
            default: () => {},
        },
    },
};
</script>
