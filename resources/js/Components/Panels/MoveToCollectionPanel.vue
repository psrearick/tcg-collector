<template>
    <ui-panel
        :show="show"
        :form="true"
        :clear="false"
        title="Move to Collection"
        save-text="Move"
        @update:show="$emit('update:show', $event)"
        @close="closePanel"
        @save="save"
    >
        <form>
            <ui-select-menu
                v-model:show="collectionMenuShow"
                v-model:selected="form.collection"
                label="Destination Collection"
                name="destination"
                class="mb-4"
                :error-message="errorMessages.collection"
                :required="true"
                :options="collectionOptions"
            />
        </form>
    </ui-panel>
</template>

<script>
import UiPanel from "@/UI/UIPanel";
import UiSelectMenu from "@/UI/Form/UISelectMenu";
import UiButton from "@/UI/UIButton";

export default {
    name: "MoveToCollectionPanel",

    components: {
        UiButton,
        UiSelectMenu,
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
        data: {
            type: Object,
            default: () => {},
        },
        errors: {
            type: Object,
            default: () => {},
        },
    },

    emits: ["update:show", "close", "saved"],

    data() {
        return {
            form: {
                originalCollection: null,
                collection: null,
                items: [],
            },
            errorMessages: {},
            collectionMenuShow: false,
            collectionOptions: [],
        };
    },

    computed: {
        saveUrl: function () {
            return `/collections/${this.form.originalCollection}/cards/move`;
        },
    },

    watch: {
        errors: function (value) {
            this.errorMessages = value;
        },
        show: function (value) {
            if (value) {
                this.form.originalCollection = this.collection.uuid;
                this.form.collection = null;
                this.form.items = this.getItems();
                return;
            }
            this.clearForm();
        },
    },

    mounted() {
        this.getCollections();
    },

    methods: {
        clearForm() {
            this.form = {
                originalCollection: null,
                collection: null,
                items: null,
            };
            this.errorMessages = {};
            this.collectionMenuShow = false;
        },
        close() {
            this.$emit("close");
            this.$emit("update:show", false);
        },
        closePanel() {
            this.clearForm();
            this.close();
        },
        save() {
            let self = this;
            axios.post(this.saveUrl, this.form).then(() => {
                this.$emit("saved");
                self.closePanel();
            });
        },
        getCollections() {
            axios.get("/collections/index").then((collections) => {
                this.collectionOptions = collections.data
                    .map((collection) => {
                        return {
                            id: collection.uuid,
                            label: collection.name,
                        };
                    })
                    .filter((collection) => {
                        return collection.uuid !== this.collection.uuid;
                    });
            });
        },
        getItems() {
            if (!this.data) {
                return [];
            }

            return this.data.data().filter((datum, key) => {
                return this.data.selectedItems().includes(key);
            });
        },
    },
};
</script>
