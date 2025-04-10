<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirm Password" />

        <div class="max-w-md w-full bg-white rounded-xl shadow-lg overflow-hidden p-8">
            <div class="text-center mb-6">
                <div class="flex justify-center mb-4">
                    <div class="p-3 bg-amber-400 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#2c3e50]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-[#2c3e50]">Confirm Password</h2>
                <p class="mt-2 text-gray-500">This is a secure area. Please confirm your password to continue.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <InputLabel for="password" value="Password" class="block text-sm font-medium text-gray-700 mb-1" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-amber-400 focus:ring focus:ring-amber-200 transition"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        autofocus
                    />
                    <InputError class="mt-1 text-sm text-red-600" :message="form.errors.password" />
                </div>

                <div class="mt-6">
                    <PrimaryButton
                        class="w-full justify-center py-2.5 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-white rounded-lg shadow-md transition-all"
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing"
                    >
                        Confirm
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
