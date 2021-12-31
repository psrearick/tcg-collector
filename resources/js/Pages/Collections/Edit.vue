<template>
    <app-layout title="Edit Collection">
        <template #header>
            <div class="flex justify-between">
                <div>
                    <h2
                        class="
                            font-semibold
                            text-xl text-gray-800
                            leading-tight
                            py-2
                        "
                    >
                        Edit {{ collection.name }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        {{ collection.description }}
                    </p>
                </div>
                <div class="flex space-x-4">
                    <Link :href="route('collections.show', collection.uuid)">
                        <ui-button
                            text="Done Editing"
                            button-style="primary-dark"
                        />
                    </Link>
                </div>
            </div>
        </template>
        <div>
            <div class="mb-12">
                <div class="flex justify-between">
                    <h3
                        class="text-lg leading-6 font-medium text-gray-900 py-4"
                    >
                        Add Cards to Collection
                    </h3>
                    <div class="py-4">
                        <Link
                            :href="
                                route('collection-set.edit', [collection.uuid])
                            "
                        >
                            <ui-button
                                text="Add Cards by Set"
                                button-style="success-outline"
                            >
                            </ui-button>
                        </Link>
                    </div>
                </div>
                <div class="w-full">
                    <collection-card-search />
                </div>
            </div>
        </div>
        <template #lowerMain>Collection Cards</template>
    </app-layout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import CollectionCardSearch from "@/Pages/Collections/Partials/CollectionCardSearch";
import UpdateCardQuantityMixin from "@/Pages/Collections/Mixins/UpdateCardQuantityMixin";
import { Link } from "@inertiajs/inertia-vue3";
import UiButton from "@/UI/UIButton";

export default {
    name: "Edit",

    components: { AppLayout, CollectionCardSearch, Link, UiButton },

    mixins: [UpdateCardQuantityMixin],

    props: {
        collection: {
            type: Object,
            default: () => {},
        },
    },

    mounted() {
        this.$store.dispatch("updateCurrentCollection", {
            collection: this.collection,
        });
    },
};
</script>
