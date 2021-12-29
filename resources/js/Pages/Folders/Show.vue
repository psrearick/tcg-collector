<template>
    <app-layout title="Collections">
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
                        {{ folder.name }}
                    </h2>
                    <p class="text-sm text-gray-500">{{ folder.description }}</p>
                </div>
                <div class="flex space-x-4">
                    <Link :href="route('folders.create', {folder: folder.uuid})">
                        <ui-button
                            text="Create Folder"
                            button-style="primary-dark"
                        />
                    </Link>

                    <Link :href="route('collections.create', {folder: folder.uuid})">
                        <ui-button
                            text="Create Collection"
                            button-style="primary-dark"
                        />
                    </Link>
                </div>
            </div>
        </template>
        <div>
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
import UiButton from "@/UI/UIButton";
export default {
    name: "Index",

    components: { AppLayout, Link, UiButton, CollectionFolderIndex },

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
    },
};
</script>
