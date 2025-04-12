<template>
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
            <Link href="/admin/users/create"
                  class="bg-[#2c3e50] hover:bg-[#34495e] text-white px-4 py-2 rounded-md text-sm font-medium">
                Add New User
            </Link>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row gap-4">
                <!-- Search Input -->
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input v-model="search" type="text" placeholder="Search users..."
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                    </div>
                </div>

                <!-- Role Filter Dropdown -->
                <div class="w-full sm:w-48">
                    <select v-model="role" @change="handleRoleChange"
                            class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm rounded-md">
                        <option value="">All Roles</option>
                        <option v-for="(label, value) in roles" :key="value" :value="value">
                            {{ label }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Joined
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="user in users.data" :key="user.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img :src="user.profile_photo_url" class="h-10 w-10 rounded-full"
                                         alt="User avatar">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ user.email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="{
                              'bg-purple-100 text-purple-800': user.role === 'admin',
                              'bg-blue-100 text-blue-800': user.role === 'librarian',
                              'bg-gray-100 text-gray-800': user.role === 'member'
                            }" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                              {{ user.role }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ new Date(user.created_at).toLocaleDateString() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <Link :href="`/admin/users/${user.id}/edit`"
                                  class="text-[#2c3e50] hover:text-[#34495e] mr-3">Edit
                            </Link>
                            <button v-if="user.id !== $page.props.auth.user.id" @click="confirmDelete(user)"
                                    class="text-red-600 hover:text-red-900">Delete
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                <Pagination :links="users.links" :from="users.from" :to="users.to" :total="users.total"
                            @navigate="handleNavigation"/>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal :show="showDeleteModal" @close="showDeleteModal = false">
            <template #title>Delete User</template>
            <template #content>Are you sure you want to delete "{{ selectedUser?.name }}"? This action cannot be
                undone.
            </template>
            <template #footer>
                <button @click="showDeleteModal = false" type="button"
                        class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2c3e50]">
                    Cancel
                </button>
                <button @click="deleteUser" type="button"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Delete
                </button>
            </template>
        </ConfirmationModal>
    </AdminLayout>
</template>

<script setup>
import {ref, watch} from 'vue';
import {Link, router} from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import {debounce} from 'lodash';

const props = defineProps({
    users: Object,
    filters: Object,
    roles: Object
});

const search = ref(props.filters.search);
const role = ref(props.filters.role || '');
const showDeleteModal = ref(false);
const selectedUser = ref(null);

// Watch for search changes
watch(search, debounce((value) => {
    router.get('/admin/users',
        {search: value, role: role.value},
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: ['users', 'filters']
        }
    );
}, 300));

// Handle role filter change
const handleRoleChange = () => {
    router.get('/admin/users',
        {search: search.value, role: role.value},
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: ['users', 'filters']
        }
    );
};

const confirmDelete = (user) => {
    selectedUser.value = user;
    showDeleteModal.value = true;
};

const deleteUser = () => {
    router.delete(`/admin/users/${selectedUser.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
        }
    });
};

const handleNavigation = async (url) => {
    if (!url) return;

    await router.visit(url, {
        preserveState: true,
        preserveScroll: true,
        only: ['users'],
        onSuccess: () => {
            router.reload({only: ['users']});
        }
    });
};
</script>

<style scoped>
select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
    padding-right: 2.5rem;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}
</style>
