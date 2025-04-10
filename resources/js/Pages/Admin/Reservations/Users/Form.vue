<template>
    <AdminLayout>
        <div class="max-w-3xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ form.id ? 'Edit User' : 'Create User' }}
                </h1>
                <Link href="/admin/users" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                    Back to Users
                </Link>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <form @submit.prevent="submit">
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                                <input v-model="form.name" type="text" id="name"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                                <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                                <input v-model="form.email" type="email" id="email"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                                <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">{{
                                        form.errors.email
                                    }}</p>
                            </div>

                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700">Role *</label>
                                <select v-model="form.role" id="role"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                                    <option value="user">User</option>
                                    <option value="librarian">Librarian</option>
                                    <option value="admin">Admin</option>
                                </select>
                                <p v-if="form.errors.role" class="mt-2 text-sm text-red-600">{{ form.errors.role }}</p>
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    {{ form.id ? 'New Password' : 'Password' }}
                                </label>
                                <input v-model="form.password" type="password" id="password"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                                <p v-if="form.errors.password" class="mt-2 text-sm text-red-600">{{
                                        form.errors.password
                                    }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-3 bg-gray-50 text-right border-t border-gray-200">
                        <button type="button" @click="reset"
                                class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2c3e50]">
                            Reset
                        </button>
                        <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#2c3e50] hover:bg-[#34495e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2c3e50]">
                            {{ form.id ? 'Update' : 'Create' }} User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import {useForm} from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    user: Object
});

const form = useForm({
    id: props.user?.id || null,
    name: props.user?.name || '',
    email: props.user?.email || '',
    role: props.user?.role || 'user',
    password: '',
    password_confirmation: ''
});

const submit = () => {
    if (form.id) {
        form.put(`/admin/users/${form.id}`);
    } else {
        form.post('/admin/users');
    }
};

const reset = () => {
    if (form.id) {
        form.name = props.user.name;
        form.email = props.user.email;
        form.role = props.user.role;
        form.password = '';
        form.password_confirmation = '';
    } else {
        form.reset();
    }
};
</script>
