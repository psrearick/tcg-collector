<template>
    <card-list classes="lg:grid-cols-4 mb:8">
        <card-list-card
            v-for="(user, userIndex) in users"
            :key="userIndex"
            :link="true"
            :href="route('group-users.show', user.id)"
        >
            <p class="mt-1 text-center text-xl font-semibold text-gray-900">
                {{ user.name }}
            </p>
            <div class="flex justify-between px-8">
                <div>
                    <p class="text-sm font-medium text-gray-500 truncate">
                        Folders
                    </p>
                    <p class="text-center">
                        {{ user.folder_count }}
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 truncate">
                        Collections
                    </p>
                    <p class="text-center">
                        {{ user.collection_count }}
                    </p>
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
    },
};
</script>
