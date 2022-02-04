<template>
    <app-layout title="Collections">
        <template #header>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $page.props.auth.user.current_team.name }}
                </h2>
            </div>
        </template>
        <template #headerRight>
            <div class="">
                <Link :href="route('groups.search.store')">
                    <ui-button
                        text="Search Group"
                        button-style="primary-dark"
                    />
                </Link>
            </div>
        </template>
        <div class="mb-8">
            <div>
                <h3 class="font-semibold text-lg text-gray-800 my-4">Users</h3>
            </div>
            <users-list
                :users="users"
                :filter-user="user"
                @updateUserId="user = $event"
            />
            <ui-button
                v-if="userId"
                text="Clear"
                button-style="success-outline"
                class="my-4"
                @click.prevent="user = null"
            />
        </div>
        <div class="mt-8">
            <h3 class="font-semibold text-lg text-gray-800 py-2">
                Collections
            </h3>
            <group-collections-data-grid
                :collections="collections"
                :user-id="user"
                :page-user="userId"
            />
        </div>
    </app-layout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import UsersList from "@/Pages/Groups/Partials/UsersList";
import GroupCollectionsDataGrid from "@/Pages/Groups/Partials/GroupCollectionsDataGrid";
import UiButton from "@/UI/UIButton";
import { Link } from "@inertiajs/inertia-vue3";

export default {
    name: "Index",

    components: {
        AppLayout,
        UsersList,
        GroupCollectionsDataGrid,
        UiButton,
        Link,
    },

    props: {
        collections: {
            type: Object,
            default: () => {},
        },
        users: {
            type: Array,
            default: () => [],
        },
        userId: {
            type: Number,
            default: null,
        },
    },

    data() {
        return {
            user: null,
        };
    },

    mounted() {
        this.user = this.userId;
    },
};
</script>
