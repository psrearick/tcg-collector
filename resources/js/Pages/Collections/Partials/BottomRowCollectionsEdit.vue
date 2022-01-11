<template>
    <transition
        enter-active-class="transform transition ease-in-out duration-500 sm:duration-700"
        enter-from-class="scale-0"
        enter-to-class="scale-100"
        leave-active-class="transform transition ease-in-out duration-500 sm:duration-700"
        leave-from-class="scale-100"
        leave-to-class="scale-0"
    >
        <div v-if="showRow" class="rounded-md">
            <div
                class="
                    bg-primary-50
                    hover:bg-primary-200
                    px-4
                    py-2
                    mb-4
                    mx-4
                    rounded-md
                    text-center
                "
            >
                <div
                    v-if="!addCardShow"
                    class="cursor-pointer"
                    @click="addCardShow = true"
                >
                    Add Card
                </div>
                <div v-if="addCardShow && newCard" class="grid md:grid-cols-4">
                    <div v-if="$settings.hasCardCondition()">
                        <ui-select-menu
                            v-model:show="conditionMenuShow['new']"
                            v-model:selected="newCard.condition"
                            label="Condition"
                            name="condition"
                            class="mb-4 px-8 mx-auto"
                            :required="false"
                            :options="conditions"
                            :center-label="true"
                        />
                    </div>
                    <div v-if="$settings.hasPriceAdded()">
                        <ui-input-label
                            label="Acquired Price"
                            :required="false"
                            class="text-center"
                        />
                        <span class="text-center block">
                            <ui-input
                                v-model="newCard.price"
                                type="currency"
                                before="$"
                                class="px-8"
                                @blur="newCard.price = currency(newCard.price)"
                            />
                        </span>
                    </div>
                    <div>
                        <ui-input-label
                            label="Quantity"
                            :required="false"
                            class="text-center"
                        />
                        <ui-input
                            v-model="newCard.quantity"
                            type="number"
                            :step="1"
                            class="px-8"
                        />
                    </div>
                    <div class="flex gap-4 mx-auto">
                        <ui-button
                            text="Cancel"
                            button-style="white"
                            class="h-10 my-auto"
                            @click="cancelAddCard"
                        />
                        <ui-button
                            :disabled="!canAddCard"
                            text="Save"
                            button-style="primary-outline"
                            class="h-10 my-auto"
                            @click="addCard"
                        />
                    </div>
                </div>
            </div>
            <div v-for="(card, cardIndex) in indexedCards" :key="cardIndex">
                <div class="bg-gray-100 mb-4 mx-4 rounded-md">
                    <div class="grid md:grid-cols-3 pt-2">
                        <div v-if="$settings.hasCardCondition()">
                            <ui-select-menu
                                v-model:show="conditionMenuShow[cardIndex]"
                                v-model:selected="condition[cardIndex]"
                                label="Condition"
                                name="condition"
                                class="mb-4 px-8 mx-auto"
                                :required="false"
                                :options="conditions"
                                :center-label="true"
                                @change="updateCondition(cardIndex)"
                            />
                        </div>
                        <div v-if="$settings.hasPriceAdded()">
                            <ui-input-label
                                label="Acquired Price"
                                :required="false"
                                class="text-center"
                            />
                            <span class="text-center block">
                                <ui-input
                                    v-model="price[cardIndex]"
                                    type="currency"
                                    before="$"
                                    class="px-8"
                                    @blur="updatePrice(cardIndex)"
                                />
                            </span>
                        </div>
                        <div>
                            <ui-input-label
                                label="Quantity"
                                :required="false"
                                class="text-center"
                            />
                            <ui-horizontal-incrementer
                                :data="card"
                                :field="fieldData"
                                class="mt-2 mx-auto"
                                @incrementQuantity="increment"
                                @decrementQuantity="decrement"
                            />
                        </div>
                    </div>
                    <jet-action-message
                        :on="
                            form &&
                            form.recentlySuccessful &&
                            form.index === cardIndex
                        "
                    >
                        <div
                            class="
                                font-bold
                                text-center
                                py-2
                                bg-success-200
                                rounded-b
                            "
                        >
                            Saved
                        </div>
                    </jet-action-message>
                </div>
            </div>
        </div>
    </transition>
</template>
<script>
import UiHorizontalIncrementer from "@/UI/Buttons/UIHorizontalIncrementer";
import UiButton from "@/UI/UIButton";
import UiSelectMenu from "@/UI/Form/UISelectMenu";
import UiInputLabel from "@/UI/Form/UIInputLabel";
import UiInput from "@/UI/Form/UIInput";
import { formatNumber } from "@/Shared/api/ConvertValue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";

export default {
    name: "BottomRowCollectionsEdit",

    components: {
        UiHorizontalIncrementer,
        UiSelectMenu,
        UiInputLabel,
        UiInput,
        UiButton,
        JetActionMessage,
    },

    props: {
        data: {
            type: Object,
            default: () => {},
        },
        field: {
            type: Object,
            default: () => {},
        },
    },

    data() {
        return {
            conditions: [
                {
                    id: 0,
                    label: "NM",
                },
                {
                    id: 1,
                    label: "LP",
                },
                {
                    id: 2,
                    label: "MP",
                },
                {
                    id: 3,
                    label: "HP",
                },
                {
                    id: 4,
                    label: "DMGD",
                },
            ],
            conditionMenuShow: [],
            conditionsIndex: [],
            condition: [],
            price: [],
            newCard: {
                _method: "POST",
                uuid: null,
                index: -1,
                id: null,
                finish: null,
                quantity: 0,
                change: 0,
                acquired_price: this.currency(0),
                price: this.currency(0),
                condition: null,
            },
            addCardShow: false,
            defaultForm: {
                _method: "POST",
                index: -1,
                id: null,
                finish: null,
                quantity: 0,
                change: 0,
                acquired_price: this.currency(0),
                price: this.currency(0),
                condition: null,
                from: {
                    finish: null,
                    acquired_price: this.currency(0),
                    condition: null,
                },
            },
            form: null,
            showRow: this.$settings.expandedDefault("edit"),
        };
    },

    computed: {
        canAddCard() {
            return (
                this.newCard.price !== null &&
                this.newCard.condition !== null &&
                this.newCard.quantity !== null &&
                this.newCard.price !== "" &&
                this.newCard.condition !== "" &&
                this.newCard.quantity !== "" &&
                this.newCard.quantity > 0
            );
        },
        fieldData() {
            let field = _.cloneDeep(this.field);
            field.componentType = "small";
            return field;
        },
        indexedCards() {
            return _.cloneDeep(this.data.cards).map((card, index) => {
                card.index = index;
                card.emit = true;
                card.from = _.cloneDeep(card);
                return card;
            });
        },
    },

    watch: {
        "data.cards": {
            deep: true,
            handler() {
                this.setCondition();
                this.setPrice();
            },
        },
    },

    created() {
        this.emitter.on("expandBottomRow", (expandData) => {
            if (expandData.hideAllRows || expandData.showAllRows) {
                this.showRow = !!this.hideAllRow;
                return;
            }
            if (
                expandData.data.uuid === this.data.uuid &&
                expandData.data.finish === this.data.finish
            ) {
                this.showRow = expandData.expand;
            }
        });
    },

    mounted() {
        this.form = this.$inertia.form(_.cloneDeep(this.defaultForm));
        this.newCard = _.cloneDeep(this.defaultForm);
        this.setCondition();
        this.setPrice();
    },

    methods: {
        decrement(card) {
            this.submitForm(card, { change: -1 });
        },
        increment(card) {
            this.submitForm(card, { change: 1 });
        },
        submitForm(card, change) {
            if (!change) {
                change = {};
            }

            let route = this.route(
                "collection-cards.store",
                card.collection_uuid
            );

            let formData = _.cloneDeep(this.defaultForm);
            Object.keys(formData).forEach((key) => {
                if (key === "from") {
                    Object.keys(formData.from).forEach((key) => {
                        let value = card.from[key]
                            ? card.from[key]
                            : formData.from[key];
                        formData.from[key] = value;
                    });
                    return;
                }
                if (key === "id") {
                    formData.id = card.uuid;
                    return;
                }
                if (key === "index") {
                    formData.index = card.index;
                    return;
                }
                const value = change[key] ? change[key] : card[key];
                formData[key] = value ? value : formData[key];
            });

            this.form = this.$inertia.form(formData);

            this.form.post(route, {
                errorBag: "updateSettings",
                preserveScroll: true,
                onSuccess: () => {
                    this.emitter.emit("trigger-collection-search");
                    if (card.index === -1) {
                        this.addCardShow = false;
                        this.newCard = _.cloneDeep(this.defaultForm);
                    }
                },
            });
        },
        addCard() {
            let collection =
                this.$store.getters.currentCollection || this.page.collection;
            let submitCard = _.cloneDeep(this.newCard);
            submitCard.index = -1;
            submitCard.uuid = this.data.uuid;
            submitCard.finish = this.data.finish;
            submitCard.acquired_price = this.unformat(this.newCard.price);
            submitCard.price = this.unformat(this.newCard.price);
            submitCard.change = this.newCard.quantity;
            submitCard.collection_uuid = collection.uuid;
            let condition = this.conditions[submitCard.condition].label;
            this.submitForm(submitCard, { condition: condition });
        },
        cancelAddCard() {
            this.newCard = _.cloneDeep(this.defaultForm);
            this.addCardShow = false;
        },
        updateCondition(index) {
            let condition = this.conditions[this.condition[index]].label;
            if (this.getChangedCondition(index)) {
                const card = this.indexedCards[index];
                this.submitForm(card, { condition: condition });
            }
        },
        updatePrice(index) {
            const price = this.price[index];
            this.price[index] = this.currency(price);
            if (this.getChangedPrice(index)) {
                const card = this.indexedCards[index];
                this.submitForm(card, { acquired_price: this.unformat(price) });
            }
        },
        format(value) {
            return value ? formatNumber(value) : "N/A";
        },
        unformat(value) {
            return Math.round(value * 100);
        },
        setCondition() {
            this.condition = this.data.cards.map((card) => {
                let index = this.conditions.findIndex(
                    (condition) => condition.label == card.condition
                );
                return index > -1 ? index : 0;
            });
        },
        setPrice() {
            this.price = this.data.cards.map((card) =>
                this.currency(card.acquired_price, {
                    fromCents: true,
                    precision: 2,
                })
            );
        },
        getChangedCondition(index) {
            const original = this.indexedCards[index].condition;
            const updated = this.conditions[this.condition[index]].label;
            return original !== updated;
        },
        getChangedPrice(index) {
            const original = this.currency(
                this.indexedCards[index].acquired_price,
                { fromCents: true, precision: 2 }
            ).value;
            const updated = this.currency(this.price[index]).value;
            return original !== updated;
        },
    },
};
</script>
