<template>
    <div>
        <div v-if="folders.length || hasParentFolder" class="mb-12">
            <card-list>
                <card-list-card-with-menu
                    v-if="hasParentFolder"
                    :href="parentFolderHref"
                    class="bg-secondary-50"
                    grid-classes="h-full"
                    main-classes="my-auto"
                >
                    <template #main>
                        <ui-icon
                            icon="arrow-narrow-up"
                            classes="mx-auto my-4"
                            size="4em"
                        />
                    </template>
                </card-list-card-with-menu>
                <card-list-card-with-menu
                    v-for="(folderItem, index) in folders"
                    :key="index"
                    :href="
                        route('folders.show', {
                            folder: folderItem.uuid,
                        })
                    "
                    :menu="getMenu(index, 'folder')"
                    class="bg-primary-50"
                >
                    <template #left>
                        <ui-icon icon="folder" />
                    </template>
                    <template #main>
                        <div class="py-4">
                            {{ folderItem.name }}
                        </div>
                        <div class="grid grid-cols-2 pb-4">
                            <div>
                                <p class="text-xs text-gray-500">Cards</p>
                                <p>
                                    {{
                                        folderItem.count ? folderItem.count : 0
                                    }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Value</p>
                                <p>{{ folderItem.value }}</p>
                            </div>
                        </div>
                    </template>
                </card-list-card-with-menu>
            </card-list>
        </div>
        <div v-if="collections.length">
            <card-list>
                <card-list-card-with-menu
                    v-for="(collection, index) in collections"
                    :key="index"
                    :href="
                        route('collections.show', {
                            collection: collection.uuid,
                        })
                    "
                    :menu="getMenu(index, 'collection')"
                >
                    <template v-if="collection.is_public" #left>
                        <span class="text-sm text-primary-500">Public</span>
                    </template>
                    <template #main>
                        <div class="py-4">
                            {{ collection.name }}
                        </div>
                        <div class="grid grid-cols-2 pb-4">
                            <div>
                                <p class="text-xs text-gray-500">Cards</p>
                                <p>
                                    {{
                                        collection.count ? collection.count : 0
                                    }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Value</p>
                                <p>{{ collection.value }}</p>
                            </div>
                        </div>
                    </template>
                </card-list-card-with-menu>
            </card-list>
        </div>
        <div
            v-if="isEmpty"
            class="bg-gray-100 p-6 rounded-md shadow-md text-center"
        >
            <p>You have no collections in this folder.</p>
        </div>
        <edit-collection-panel
            v-model:show="showEditCollectionPanel"
            :collection="editCollection"
            :type="editCollectionType"
        />
        <delete-collection-panel
            v-model:show="showDeleteCollectionPanel"
            :collection="editCollection"
            :type="editCollectionType"
        />
        <move-item-panel
            v-model:show="showMovePanel"
            :collection="editCollection"
            :folder="folder"
            :type="editCollectionType"
        />
        <public-link-modal
            v-model:show="showPublicLinkModel"
            :collection="editCollection"
        />
    </div>
</template>

<script>
import CardList from "@/Components/CardLists/CardList";
import CardListCardWithMenu from "@/Components/CardLists/CardListCardWithMenu";
import EditCollectionPanel from "@/Components/Panels/EditCollectionPanel";
import DeleteCollectionPanel from "@/Components/Panels/DeleteCollectionPanel";
import MoveItemPanel from "@/Components/Panels/MoveItemPanel";
import PublicLinkModal from "@/Components/Modals/PublicLinkModal.vue";
import UiIcon from "@/UI/UIIcon";

export default {
    name: "CollectionFolderIndex",

    components: {
        CardList,
        CardListCardWithMenu,
        EditCollectionPanel,
        DeleteCollectionPanel,
        PublicLinkModal,
        UiIcon,
        MoveItemPanel,
    },

    props: {
        collections: {
            type: Array,
            default: () => [],
        },
        folder: {
            type: Object,
            default: () => {},
        },
        folders: {
            type: Array,
            default: () => [],
        },
    },

    data() {
        return {
            showEditCollectionPanel: false,
            showDeleteCollectionPanel: false,
            showMovePanel: false,
            showPublicLinkModel: false,
            editCollection: {},
            editCollectionType: "collection",
        };
    },

    computed: {
        hasParentFolder() {
            return this.folder;
        },
        parentFolderHref() {
            if (!this.folder) {
                return "";
            }

            if (!this.folder.parent_uuid) {
                return route("collections.index");
            }

            return route("folders.show", this.folder.parent_uuid);
        },
        isEmpty() {
            return this.folders.length === 0 && this.collections.length === 0;
        },
    },

    created() {
        this.emitter.on("dropdown_link_click", (clickData) => {
            this.linkClicked(clickData);
        });
    },

    methods: {
        linkClicked(clickData) {
            this.editCollection = clickData.collection;
            this.editCollectionType = clickData.type;
            if (clickData.action === "edit") {
                this.showEditCollectionPanel = true;
                return;
            }

            if (clickData.action === "delete") {
                this.showDeleteCollectionPanel = true;
                return;
            }

            if (clickData.action === "move") {
                this.showMovePanel = true;
                return;
            }

            if (clickData.action === "getLink") {
                this.showPublicLinkModel = true;
                return;
            }
        },
        getMenu(index, type) {
            const menus = [
                {
                    content: "Edit",
                    action: "edit",
                    collection:
                        type === "collection"
                            ? this.collections[index]
                            : this.folders[index],
                    type: type,
                },
                {
                    content: "Delete",
                    action: "delete",
                    collection:
                        type === "collection"
                            ? this.collections[index]
                            : this.folders[index],
                    type: type,
                },
                {
                    content: "Move",
                    action: "move",
                    collection:
                        type === "collection"
                            ? this.collections[index]
                            : this.folders[index],
                    type: type,
                },
                {
                    content: "Get Public Link",
                    action: "getLink",
                    collection: this.collections[index],
                    restriction: "collection",
                    type: type,
                },
            ];
            return menus.filter((menu) => {
                return !menu.restriction || menu.restriction === type;
            });
        },
    },
};
</script>
