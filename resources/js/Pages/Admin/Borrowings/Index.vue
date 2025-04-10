<template>
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Borrowing Management</h1>
            <Link href="/admin/borrowings/create"
                  class="bg-[#2c3e50] hover:bg-[#34495e] text-white px-4 py-2 rounded-md text-sm font-medium">
                Manual Checkout
            </Link>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input v-model="search" type="text" placeholder="Search borrowings..."
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                        </div>
                    </div>
                    <div class="ml-4">
                        <select v-model="statusFilter"
                                class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm rounded-md">
                            <option value="active">Active</option>
                            <option value="overdue">Overdue</option>
                            <option value="returned">Returned</option>
                            <option value="">All</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Borrowed
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due
                            Date
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="borrowing in borrowings.data" :key="borrowing.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-2">
                                <Link :href="route('admin.borrowings.show', borrowing.id)"
                                      class="text-[#2c3e50] hover:text-[#34495e] mr-3">
                                    View
                                </Link>
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img v-if="borrowing.book.cover_image_url" :src="borrowing.book.cover_image_url"
                                         class="h-10 w-10 rounded object-cover">
                                    <div v-else class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24"
                                             stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ borrowing.book.title }}</div>
                                    <div class="text-sm text-gray-500">{{ borrowing.book.author }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ borrowing.user.name }}</div>
                            <div class="text-sm text-gray-500">{{ borrowing.user.email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ new Date(borrowing.borrowed_at).toLocaleDateString() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ new Date(borrowing.due_date).toLocaleDateString() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
    <span :class="{
        'bg-green-100 text-green-800': !borrowing.returned_at && !borrowing.is_overdue,
        'bg-red-100 text-red-800': borrowing.is_overdue,
        'bg-gray-100 text-gray-800': borrowing.returned_at
    }" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
        {{ borrowing.returned_at ? 'Returned' : (borrowing.is_overdue ? 'Overdue' : 'Active') }}
    </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button
                                v-if="!borrowing.returned_at"
                                @click="openReturnModal(borrowing)"
                                class="text-[#2c3e50] hover:text-[#34495e] mr-3"
                            >
                                Mark Returned
                            </button>
                            <!-- Renew Button - Show for active borrowings that aren't overdue -->
                            <button
                                v-if="!borrowing.returned_at && !borrowing.is_overdue"
                                @click="openRenewModal(borrowing)"
                                class="text-blue-600 hover:text-blue-900"
                            >
                                Renew
                            </button>
                            <!-- View Details Button - Show for returned borrowings -->
                            <Link
                                v-if="borrowing.returned_at"
                                :href="`/admin/borrowings/${borrowing.id}`"
                                class="text-gray-600 hover:text-gray-900"
                            >
                                View Details
                            </Link>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">

                <Pagination :links="borrowings.links"
                            :from="borrowings.from"
                            :to="borrowings.to"
                            total="borrowings.total"
                            @navigate="handleNavigation"/>
            </div>
        </div>

        <!-- Return Confirmation Modal -->
        <ConfirmationModal :show="showReturnModal" @close="showReturnModal = false">
            <template #title>Mark as Returned</template>
            <template #content>Are you sure you want to mark "{{ selectedBorrowing?.book.title }}" as returned?
            </template>
            <template #footer>
                <button @click="showReturnModal = false" type="button"
                        class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2c3e50]">
                    Cancel
                </button>
                <button @click="markReturned" type="button"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#2c3e50] hover:bg-[#34495e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2c3e50]">
                    Confirm Return
                </button>
            </template>
        </ConfirmationModal>

        <!-- Renew Confirmation Modal -->
        <ConfirmationModal :show="showRenewModal" @close="showRenewModal = false">
            <template #title>Renew Borrowing</template>
            <template #content>
                <p class="mb-4">Are you sure you want to renew "{{ selectedBorrowing?.book.title }}"?</p>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">New Due Date</label>
                        <p class="mt-1 text-sm text-gray-500">{{ newDueDate }}</p>
                    </div>
                </div>
            </template>
            <template #footer>
                <button @click="showRenewModal = false" type="button"
                        class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2c3e50]">
                    Cancel
                </button>
                <button @click="renewBorrowing" type="button"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#2c3e50] hover:bg-[#34495e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2c3e50]">
                    Confirm Renewal
                </button>
            </template>
        </ConfirmationModal>
    </AdminLayout>
</template>

<script setup>
import {computed, ref, watch} from 'vue';
import {Link, router} from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import {debounce} from 'lodash';

const props = defineProps({
    borrowings: Object,
    filters: Object
});

const search = ref(props.filters.search);
const statusFilter = ref(props.filters.status);
const showReturnModal = ref(false);
const showRenewModal = ref(false);
const selectedBorrowing = ref(null);

// Calculate new due date (2 weeks from today)
const newDueDate = computed(() => {
    const date = new Date();
    date.setDate(date.getDate() + 14);
    return date.toLocaleDateString();
});

watch([search, statusFilter], debounce(([searchValue, statusValue]) => {
    router.get('/admin/borrowings', {
        search: searchValue,
        status: statusValue
    }, {
        preserveState: true,
        replace: true
    });
}, 300));

const openReturnModal = (borrowing) => {
    if (!borrowing.returned_at) {
        selectedBorrowing.value = borrowing;
        showReturnModal.value = true;
    }
};

const markReturned = () => {
    if (!selectedBorrowing.value) return;

    router.put(`/admin/borrowings/${selectedBorrowing.value.id}/return`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            showReturnModal.value = false;
            selectedBorrowing.value = null;
        }
    });
};

const openRenewModal = (borrowing) => {
    if (!borrowing.returned_at && !borrowing.is_overdue) {
        selectedBorrowing.value = borrowing;
        showRenewModal.value = true;
    }
};

const renewBorrowing = () => {
    if (!selectedBorrowing.value) return;

    router.put(`/admin/borrowings/${selectedBorrowing.value.id}/renew`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            showRenewModal.value = false;
            selectedBorrowing.value = null;
        }
    });
};

const handleNavigation = async (url) => {
    if (!url) return;

    await router.visit(url, {
        preserveState: true,
        preserveScroll: true,
        only: ['borrowings'],
        onSuccess: () => {
            // Force update if needed
            router.reload({only: ['borrowings']});
        }
    });
};
</script>
