<template>
    <AdminLayout>
        <div class="px-4 py-6 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-xl font-semibold text-gray-900">Overdue Borrowings</h1>
                    <p class="mt-2 text-sm text-gray-700">
                        Showing all overdue book borrowings
                    </p>
                </div>
            </div>

            <!-- Overdue Table -->
            <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrowed</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Overdue</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="borrowing in borrowings.data" :key="borrowing.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ borrowing.book.title }}</div>
                                <div class="text-sm text-gray-500">{{ borrowing.book.author }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ borrowing.user.name }}</div>
                                <div class="text-sm text-gray-500">{{ borrowing.user.email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDate(borrowing.borrowed_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDate(borrowing.due_date) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                    {{ daysOverdue(borrowing.due_date) }} days
                  </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="borrowings.links" class="mt-4 px-6 py-3" />
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    borrowings: Object,
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};

const daysOverdue = (dueDate) => {
    const today = new Date();
    const due = new Date(dueDate);
    const diffTime = today - due;
    return Math.floor(diffTime / (1000 * 60 * 60 * 24));
};
</script>
