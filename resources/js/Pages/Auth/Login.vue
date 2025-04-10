<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in"/>

        <div class="max-w-md w-full bg-white rounded-xl shadow-lg overflow-hidden p-8">
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <div class="p-3 bg-amber-400 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#2c3e50]" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-[#2c3e50] mb-2">Welcome back</h2>
                <p class="text-gray-500">Sign in to your account</p>
            </div>

            <div v-if="status" class="mb-4 px-4 py-3 bg-green-50 text-green-700 rounded-lg">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">
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

                <div>
                    <div class="flex justify-between items-center">
                        <InputLabel for="password" value="Password"
                                    class="block text-sm font-medium text-gray-700 mb-1"/>
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm text-amber-600 hover:text-amber-500"
                        >
                            Forgot password?
                        </Link>
                    </div>
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-amber-400 focus:ring focus:ring-amber-200 transition"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    />
                    <InputError class="mt-1 text-sm text-red-600" :message="form.errors.password"/>
                </div>

                <div class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember"
                              class="rounded border-gray-300 text-amber-500 focus:ring-amber-400"/>
                    <InputLabel for="remember" class="ms-2 text-sm text-gray-600">Remember me</InputLabel>
                </div>

                <div>
                    <PrimaryButton
                        class="w-full justify-center py-2.5 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-white rounded-lg shadow-md transition-all"
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing"
                    >
                        <span v-if="!form.processing">Log in</span>
                        <span v-else class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </PrimaryButton>
                </div>
            </form>

            <div class="mt-6 text-center text-sm text-gray-500">
                Don't have an account?
                <Link :href="route('register')" class="font-medium text-amber-600 hover:text-amber-500">
                    Sign up
                </Link>
            </div>
        </div>
    </GuestLayout>
</template>
