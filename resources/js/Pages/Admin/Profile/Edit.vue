<template>
    <AdminLayout title="Profile">
        <div class="max-w-3xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <!-- Profile Photo -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center space-x-4">
                        <img :src="user.profile_photo_url" class="h-20 w-20 rounded-full object-cover">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">{{ user.name }}</h2>
                            <p class="text-gray-600">{{ user.email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Profile Update Form -->
                <form @submit.prevent="submit">
                    <div class="p-6 space-y-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input v-model="form.name" id="name" type="text"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                            <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input v-model="form.email" id="email" type="email"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                            <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">{{ form.errors.email }}</p>
                        </div>

                        <!-- Photo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Photo</label>
                            <div class="mt-2 flex items-center">
                                <input ref="photoInput" type="file" class="hidden" @change="updatePhotoPreview">
                                <button type="button" @click="selectNewPhoto"
                                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2c3e50] text-sm">
                                    Select New Photo
                                </button>
                                <button v-if="photoPreview" type="button" @click="removePhoto"
                                        class="ml-2 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 text-sm">
                                    Remove
                                </button>
                            </div>
                            <div v-if="photoPreview" class="mt-2">
                                <img :src="photoPreview" class="h-20 w-20 rounded-full object-cover">
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900">Update Password</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700">Current
                                        Password</label>
                                    <input v-model="form.current_password" id="current_password" type="password"
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                                    <p v-if="form.errors.current_password" class="mt-2 text-sm text-red-600">
                                        {{ form.errors.current_password }}</p>
                                </div>
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">New
                                        Password</label>
                                    <input v-model="form.password" id="password" type="password"
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                                    <p v-if="form.errors.password" class="mt-2 text-sm text-red-600">
                                        {{ form.errors.password }}</p>
                                </div>
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm
                                        Password</label>
                                    <input v-model="form.password_confirmation" id="password_confirmation"
                                           type="password"
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-6 py-3 bg-gray-50 text-right border-t border-gray-200">
                        <button type="button" @click="resetForm"
                                class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2c3e50]">
                            Reset
                        </button>
                        <button type="submit" :disabled="form.processing"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#2c3e50] hover:bg-[#34495e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2c3e50] disabled:opacity-50">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import {ref} from 'vue';
import {useForm} from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    user: Object,
});

const photoInput = ref(null);
const photoPreview = ref(null);

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    photo: null,
    current_password: '',
    password: '',
    password_confirmation: '',
});

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];
    if (!photo) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(photo);

    form.photo = photo;
};

const removePhoto = () => {
    photoInput.value.value = null;
    photoPreview.value = null;
    form.photo = null;
};

const resetForm = () => {
    form.reset();
    removePhoto();
};

const submit = () => {
    form.post(route('admin.profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            resetForm();
        },
    });
};
</script>
