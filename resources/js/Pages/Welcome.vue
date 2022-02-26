<template>
    <div>
        <div class="w-full text-center pt-32 px-16 pb-16">
            <ui-icon
                icon="collection"
                size="14rem"
                classes="text-gray-500 inline-block"
            />
            <h1 class="text-4xl mt-4 text-gray-600">MTG Collector</h1>
        </div>

        <div v-if="canLogin" class="px-6 py-4 block">
            <div class="flex justify-around max-w-sm mx-auto">
                <Link
                    v-if="$page.props.auth.user"
                    href="/dashboard"
                    class="text-sm text-gray-700 underline"
                >
                    <ui-button button-style="primary-dark" text="Dashboard" />
                </Link>

                <div v-else class="flex gap-12">
                    <Link :href="route('login')">
                        <ui-button button-style="primary-dark" text="Log in" />
                    </Link>

                    <Link v-if="canRegister" :href="route('register')">
                        <ui-button
                            button-style="primary-dark"
                            text="Register"
                        />
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Layout from "@/Layouts/Layout";
import UiIcon from "@/UI/UIIcon";
import UiButton from "@/UI/UIButton";
import { Link } from "@inertiajs/inertia-vue3";

export default {
    components: { UiIcon, UiButton, Link },
    layout: Layout,
    props: {
        auth: { type: Object, default: () => {} },
        canLogin: Boolean,
        canRegister: Boolean,
        errors: { type: Object, default: () => {} },
    },
};
</script>
