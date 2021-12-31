<template>
    <card-list classes="lg:grid-cols-4 mb-8">
        <card-list-card>
            <dt class="text-sm font-medium text-gray-500 truncate">
                Total Cards
            </dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">
                {{ summary.total_cards }} Cards
            </dd>
        </card-list-card>
        <card-list-card>
            <dt class="text-sm font-medium text-gray-500 truncate">
                Current Value
            </dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">
                {{ formattedCurrency(summary.current_value) }}
            </dd>
        </card-list-card>
        <card-list-card>
            <dt class="text-sm font-medium text-gray-500 truncate">
                Acquired Value
            </dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">
                {{ formattedCurrency(summary.acquired_value) }}
            </dd>
        </card-list-card>
        <card-list-card>
            <dt class="text-sm font-medium text-gray-500 truncate">
                Gain/Loss
            </dt>
            <dd
                class="mt-1 text-3xl font-semibold"
                :class="
                    summary.gain_loss >= 0 ? 'text-gray-900' : 'text-red-500'
                "
            >
                {{ formattedCurrency(summary.gain_loss) }} ({{
                    formattedPercentage(summary.gain_loss_percent)
                }})
            </dd>
        </card-list-card>
    </card-list>
</template>

<script>
import CardList from "@/Components/CardLists/CardList";
import CardListCard from "@/Components/CardLists/CardListCard";
import { formatCurrency, formatPercentage } from "@/Shared/api/ConvertValue";

export default {
    name: "CollectionsShowCardList",

    components: {
        CardList,
        CardListCard,
    },

    props: {
        summary: {
            type: Object,
            default: () => {},
        },
    },

    methods: {
        formattedCurrency(value) {
            return formatCurrency(value);
        },
        formattedPercentage(value) {
            return formatPercentage(value, 2, true, true);
        },
    },
};
</script>
