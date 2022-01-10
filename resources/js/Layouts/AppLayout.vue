<template>
    <div>
        <Head :title="title" />

        <jet-banner />

        <div class="min-h-screen bg-gray-100">
            <navigation />

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div
                        :class="$slots.headerRight ? 'grid md:grid-cols-2' : ''"
                    >
                        <div class="py-2">
                            <slot name="header" />
                        </div>
                        <div class="flex space-x-4 md:justify-end mt-4 md:mt-0">
                            <slot name="headerRight" />
                        </div>
                    </div>
                </div>
            </header>
            <main class="py-12">
                <div v-if="$slots.default">
                    <div class="max-w-7xl mx-auto md:px-6 lg:px-8">
                        <div
                            class="
                                bg-white
                                md:rounded-lg
                                shadow
                                px-2
                                py-6
                                sm:px-6
                            "
                        >
                            <slot></slot>
                        </div>
                    </div>
                </div>

                <div
                    v-if="mainSlots.length"
                    :class="$slots.default ? 'pt-6' : ''"
                >
                    <div
                        v-for="(card, cardIndex) in mainSlots"
                        :key="cardIndex"
                        class="max-w-7xl mx-auto py-6 md:px-6 lg:px-8"
                    >
                        <div
                            class="
                                bg-white
                                md:rounded-lg
                                shadow
                                px-2
                                py-6
                                sm:px-6
                            "
                        >
                            <slot :name="card"></slot>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import JetBanner from "@/Jetstream/Banner.vue";
import { Head } from "@inertiajs/inertia-vue3";
import Navigation from "@/Layouts/Partials/Navigation";

export default defineComponent({
    components: {
        Head,
        JetBanner,
        Navigation,
    },
    props: {
        title: {
            type: String,
            default: "",
        },
        mainSlots: {
            type: Array,
            default: () => [],
        },
    },
});
</script>
