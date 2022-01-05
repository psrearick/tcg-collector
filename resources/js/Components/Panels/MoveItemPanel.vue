<template>
    <ui-panel
        :show="show"
        :form="true"
        :clear="false"
        :title="title"
        save-text="Move"
        @update:show="$emit('update:show', $event)"
        @close="closePanel"
        @save="save"
    >
        <p>
            <span>Where would you like to move </span>
            <span class="text-gray-500 text-sm py-4 font-bold">{{
                collection.name
            }}</span>
            <span>?</span>
        </p>
        <form>
            <ui-select-menu
                v-model:show="destinationMenuShow"
                v-model:selected="form.destination"
                label="Destination"
                name="destination"
                class="mb-4"
                :required="true"
                :options="getFolders()"
            />
        </form>
    </ui-panel>
</template>

<script>
import UiPanel from "@/UI/UIPanel";
import UiInput from "@/UI/Form/UIInput";
import UiSelectMenu from "@/UI/Form/UISelectMenu";

export default {
    name: "MoveItemPanel",

    components: {
        UiSelectMenu,
        UiInput,
        UiPanel,
    },

    props: {
        show: {
            type: Boolean,
            default: false,
        },
        collection: {
            type: Object,
            default: () => {},
        },
        folder: {
            type: Object,
            default: () => {},
        },
        type: {
            type: String,
            default: "",
        },
    },

    emits: ["update:show", "close"],

    data() {
        return {
            // collectionOptions: [],
            destinationMenuShow: false,
            form: {
                destination: null,
                uuid: null,
                type: null,
            },
            list: [],
            folders: [],
        };
    },

    computed: {
        title() {
            return (
                "Move " + (this.type === "collection" ? "Collection" : "Folder")
            );
        },
        thisFolder() {
            if (this.type === "collection") {
                return {};
            }

            return this.folders.find(
                (folder) => folder.id === this.collection.id
            );
        },
        moveUrl() {
            return this.type === "collection"
                ? "/collections/move"
                : "/folders/move";
        },
    },

    watch: {
        show: function (value) {
            if (value) {
                this.form.uuid = this.collection.uuid;
                this.form.type = this.type;
                this.getFolders();
                return;
            }
            this.clearForm();
        },
    },

    methods: {
        clearForm() {
            this.form = {
                uuid: null,
                destination: null,
                type: "",
            };
            this.folders = [];
            this.destinationMenuShow = false;
        },
        close() {
            this.$emit("close");
            this.$emit("update:show", false);
        },
        closePanel() {
            this.clearForm();
            this.close();
        },
        getFolders() {
            if (!this.collection) {
                return [];
            }
            if (!this.collection.allowed) {
                return [];
            }

            let path = this.collection.allowed.map(function (folder) {
                return { id: folder.uuid, label: folder.path };
            });

            return path;
        },
        save() {
            let self = this;
            this.$inertia.visit(this.moveUrl, {
                method: "patch",
                data: this.form,
                preserveState: true,
                onSuccess: () => {
                    self.closePanel();
                },
            });
        },
    },
};
</script>
