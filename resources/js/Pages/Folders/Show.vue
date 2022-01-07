<template>
    <app-layout title="Collections">
        <template #header>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ folder.name }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ folder.description }}
                </p>
            </div>
        </template>
        <template #headerRight>
            <div class="flex space-x-4">
                <Link :href="route('folders.create', { folder: folder.uuid })">
                    <ui-button
                        text="Create Folder"
                        button-style="primary-dark"
                    />
                </Link>

                <Link
                    :href="route('collections.create', { folder: folder.uuid })"
                >
                    <ui-button
                        text="Create Collection"
                        button-style="primary-dark"
                    />
                </Link>
            </div>
        </template>
        <div>
            <folder-summary :summary="totals" />
            <collection-folder-index
                :collections="collections"
                :folder="folder"
                :folders="folders"
            />
        </div>
    </app-layout>
</template>
<script>
import { Link } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import CollectionFolderIndex from "@/Components/CardLists/CollectionFolderIndex";
import FolderSummary from "@/Components/CardLists/FolderSummary";
import UiButton from "@/UI/UIButton";
export default {
    name: "Index",

    components: {
        AppLayout,
        Link,
        UiButton,
        CollectionFolderIndex,
        FolderSummary,
    },

    props: {
        folder: {
            type: Object,
            default: () => {},
        },
        folders: {
            type: Object,
            default: () => {},
        },
        collections: {
            type: Object,
            default: () => {},
        },
        totals: {
            type: Object,
            default: () => {},
        },
    },
};
</script>
