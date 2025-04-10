<script setup>
import {computed} from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification"/>

        <div class="max-w-md w-full bg-white rounded-xl shadow-lg overflow-hidden p-8">
            <div class="text-center mb-6">
                <div class="flex justify-center mb-4">
                    <div class="p-3 bg-amber-400 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#2c3e50]" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-[#2c3e50]">Verify Your Email</h2>
            </div>

            <div class="mb-6 text-sm text-gray-600 bg-blue-50 p-4 rounded-lg">
                Thanks for signing up! Before getting started, please verify your email address by clicking the link we
                sent you.
            </div>

            <div v-if="verificationLinkSent" class="mb-6 text-sm font-medium text-green-600 bg-green-50 p-4 rounded-lg">
                A new verification link has been sent to your email address.
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div class="flex items-center justify-between">
                    <PrimaryButton
                        class="py-2.5 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-white rounded-lg shadow-md transition-all px-6"
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing"
                    >
                        Resend Verification Email
                    </PrimaryButton>

                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="text-sm text-amber-600 hover:text-amber-500 font-medium"
                    >
                        Log Out
                    </Link>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
