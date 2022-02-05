<template>
    <card-list classes="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        <ui-card
            v-for="(user, userIndex) in users"
            :key="userIndex"
            :active="filterUser === user.id"
            :mode="filterUser === user.id ? 'primary' : 'default'"
            padding="p-4"
            class="cursor-pointer"
            card-style="white"
            @click.prevent="filterCollections(user.id)"
        >
            <div>
                <div class="grid grid-cols-4">
                    <p
                        class="
                            mt-1
                            text-center text-xl
                            font-semibold
                            text-gray-900
                            col-span-2 col-start-2
                        "
                    >
                        {{ user.name }}
                    </p>
                    <div class="text-right">
                        <ui-icon
                            v-if="filterUser === user.id"
                            icon="solid-circle-check"
                            class="text-primary-500 inline-block"
                            size="1.5rem"
                        />
                    </div>
                </div>
                <div class="flex justify-between">
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
        </ui-card>
    </card-list>
</template>
<script>
import CardList from "@/Components/CardLists/CardList";
import CardListCard from "@/Components/CardLists/CardListCard";
import UiCard from "@/UI/UICard";
import UiIcon from "@/UI/UIIcon";

export default {
    name: "UsersListBackup",

    components: {
        UiCard,
        UiIcon,
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
