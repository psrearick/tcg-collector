<template>
    <app-layout title="Edit Collection" :main-slots="slotNames">
        <template #header>
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
        </template>
        <template #headerRight>
            <div>
                <Link :href="route('collections.show', collection.uuid)">
                    <ui-button
                        text="Done Editing"
                        button-style="primary-dark"
                    />
                </Link>
            </div>
        </template>
        <template #CollectionDetails>
            <h3 class="text-lg leading-6 font-medium text-gray-900 py-4">
                Collection Details
            </h3>
            <update-collection-details-form :collection="collection" />
        </template>
        <template #AddCards>
            <div>
                <div class="mb-12">
                    <div class="flex justify-between">
                        <h3
                            class="
                                text-lg
                                leading-6
                                font-medium
                                text-gray-900
                                py-4
                            "
                        >
                            Add Cards to Collection
                        </h3>
                        <div class="py-4">
                            <Link
                                :href="
                                    route('collection-set.show', [
                                        collection.uuid,
                                    ])
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
        </template>

        <template #CollectionCards>
            <h3 class="text-lg leading-6 font-medium text-gray-900 py-4">
                Collection Cards
            </h3>
            <collections-data-grid
                :collection="collection"
                :table="table"
                :search-url="searchUrl"
            />
        </template>
    </app-layout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import CollectionCardSearch from "@/Pages/Collections/Partials/CollectionCardSearch";
import CollectionsDataGrid from "@/Pages/Collections/Partials/CollectionsDataGrid";
import UpdateCardQuantityMixin from "@/Pages/Collections/Mixins/UpdateCardQuantityMixin";
import CollectionsEditTableMixin from "@/Pages/Collections/Mixins/CollectionsEditTableMixin";
import UpdateCollectionDetailsForm from "@/Pages/Collections/Partials/UpdateCollectionDetailsForm";
import { Link } from "@inertiajs/inertia-vue3";
import UiButton from "@/UI/UIButton";

export default {
    name: "Edit",

    components: {
        AppLayout,
        CollectionCardSearch,
        CollectionsDataGrid,
        Link,
        UiButton,
        UpdateCollectionDetailsForm,
    },

    mixins: [UpdateCardQuantityMixin, CollectionsEditTableMixin],

    props: {
        collection: {
            type: Object,
            default: () => {},
        },
    },

    data() {
        return {
            slotNames: ["CollectionDetails", "AddCards", "CollectionCards"],
            form: {
                name: "",
                description: "",
                id: null,
                is_public: false,
            },
        };
    },

    mounted() {
        this.$store.dispatch("updateCurrentCollection", {
            collection: this.collection,
        });

        this.form = _.cloneDeep(this.collection);
    },
};
</script>
