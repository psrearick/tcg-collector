<template>
    <transition
        enter-active-class="transform transition ease-in-out duration-500 sm:duration-700"
        enter-from-class="scale-0"
        enter-to-class="scale-100"
        leave-active-class="transform transition ease-in-out duration-500 sm:duration-700"
        leave-from-class="scale-100"
        leave-to-class="scale-0"
    >
        <div v-if="showRow" class="ml-20">
            <div v-for="(card, cardIndex) in data.cards" :key="cardIndex">
                <div>
                    <div
                        class="
                            grid
                            md:grid-cols-3
                            py-1
                            border-t-2 border-gray-100
                        "
                    >
                        <div v-if="$settings.hasCardCondition()" class="flex">
                            <span class="text-sm text-gray-500">Condition</span>
                            <span class="ml-4">{{ card.condition }}</span>
                        </div>
                        <div v-if="$settings.hasPriceAdded()" class="flex">
                            <span class="text-sm text-gray-500"
                                >Acquired Price</span
                            >
                            <span class="ml-4">{{
                                $convertValue.formatCurrency(
                                    card.acquired_price
                                )
                            }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-sm text-gray-500">quantity</span>
                            <span class="ml-4">{{ card.quantity }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>
<script>
export default {
    name: "BottomRowCollectionsShow",

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
            showRow: this.$settings.expandedDefault("show"),
        };
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
};
</script>
