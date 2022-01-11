<template>
    <card-list classes="lg:grid-cols-4">
        <card-list-card
            v-for="(user, userIndex) in users"
            :key="userIndex"
            :class="filterUser === user.id ? 'bg-gray-200' : ''"
            :link="true"
            @click.prevent="filterCollections(user.id)"
        >
            <div>
                <p
                    class="
                        mt-1
                        text-center text-xl
                        font-semibold
                        text-gray-900
                        p-4
                    "
                >
                    {{ user.name }}
                </p>
                <div class="flex justify-between p-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500 truncate">
                            Value
                        </p>
                        <p class="text-center">
                            {{ user.display_current_value }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 truncate">
                            Cards
                        </p>
                        <p class="text-center">
                            {{ user.total_cards || 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </card-list-card>
    </card-list>
</template>
<script>
import CardList from "@/Components/CardLists/CardList";
import CardListCard from "@/Components/CardLists/CardListCard";

export default {
    name: "UsersList",

    components: {
        CardList,
        CardListCard,
    },

    props: {
        users: {
            type: Array,
            default: () => [],
        },
        filterUser: {
            type: Number,
            default: null,
        },
    },

    emits: ["updateUserId"],

    methods: {
        filterCollections(userId) {
            this.$emit("updateUserId", userId);
        },
    },
};
</script>
