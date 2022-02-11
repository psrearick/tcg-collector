<template>
    <Head title="Email Verification" />

    <jet-authentication-card>
        <template #logo>
            <jet-authentication-card-logo />
        </template>

        <div class="mb-4 text-sm text-gray-600">
            Thanks for signing up! Before getting started, could you verify your
            email address by clicking on the link we just emailed to you? If you
            didn't receive the email, we will gladly send you another.
        </div>

        <div
            v-if="verificationLinkSent"
            class="mb-4 font-medium text-sm text-green-600"
        >
            A new verification link has been sent to the email address you
            provided during registration.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <ui-button
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    button-style="primary-dark"
                    type="submit"
                >
                    Resend Verification Email
                </ui-button>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="underline text-sm text-gray-600 hover:text-gray-900"
                    >Log Out</Link
                >
            </div>
        </form>
    </jet-authentication-card>
</template>

<script>
import { defineComponent } from "vue";
import JetAuthenticationCard from "@/Jetstream/AuthenticationCard.vue";
import JetAuthenticationCardLogo from "@/Jetstream/AuthenticationCardLogo.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import UiButton from "@/UI/UIButton";

export default defineComponent({
    components: {
        UiButton,
        Head,
        JetAuthenticationCard,
        JetAuthenticationCardLogo,
        Link,
    },

    props: {
        status: {
            type: String,
            default: "",
        },
    },

    data() {
        return {
            form: this.$inertia.form(),
        };
    },

    computed: {
        verificationLinkSent() {
            return this.status === "verification-link-sent";
        },
    },

    methods: {
        submit() {
            this.form.post(this.route("verification.send"));
        },
    },
});
</script>
