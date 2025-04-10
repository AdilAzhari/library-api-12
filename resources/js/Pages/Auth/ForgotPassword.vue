<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {Head, useForm} from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password"/>

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
                <h2 class="text-2xl font-bold text-[#2c3e50]">Reset Password</h2>
                <p class="mt-2 text-gray-500">Enter your email to receive a reset link.</p>
            </div>

            <div v-if="status" class="mb-4 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <InputLabel for="email" value="Email" class="block text-sm font-medium text-gray-700 mb-1"/>
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-amber-400 focus:ring focus:ring-amber-200 transition"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <InputError class="mt-1 text-sm text-red-600" :message="form.errors.email"/>
                </div>

                <div class="mt-6">
                    <PrimaryButton
                        class="w-full justify-center py-2.5 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-white rounded-lg shadow-md transition-all"
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing"
                    >
                        Send Reset Link
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
