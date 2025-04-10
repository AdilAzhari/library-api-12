<template>
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Book Management</h1>
            <Link href="/admin/books/create"
                  class="bg-[#2c3e50] hover:bg-[#34495e] text-white px-4 py-2 rounded-md text-sm font-medium">
                Add New Book
            </Link>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input v-model="search" type="text" placeholder="Search books..."
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                    </div>
                </div>
                <div class="ml-4">
                    <select v-model="statusFilter"
                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm rounded-md">
                        <option value="">All Statuses</option>
                        <option value="available">Available</option>
                        <option value="borrowed">Borrowed</option>
                        <option value="reserved">Reserved</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cover
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Author
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
                    <tr v-for="book in books.data" :key="book.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="h-10 w-10 flex-shrink-0">
                                <img v-if="book.cover_image" :src="book.cover_image_url"
                                     class="h-10 w-10 rounded object-cover"
                                     alt="Book cover">
                                <div v-else class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ book.title }}</div>
                            <div class="text-sm text-gray-500">{{ book.isbn }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ book.author }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                <span :class="{
                  'bg-green-100 text-green-800': book.status === 'available',
                  'bg-yellow-100 text-yellow-800': book.status === 'reserved',
                  'bg-red-100 text-red-800': book.status === 'borrowed'
                }" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ book.status }}
                </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <Link :href="`/admin/books/${book.id}/edit`"
                                  class="text-[#2c3e50] hover:text-[#34495e] mr-3">Edit
                            </Link>
                            <button @click="confirmDelete(book)" class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                <Pagination
                    :links="books.links"
                    :from="books.from"
                    :to="books.to"
                    :total="books.total"
                    @navigate="handleNavigation"
                />
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal :show="showDeleteModal" @close="showDeleteModal = false">
            <template #title>Delete Book</template>
            <template #content>Are you sure you want to delete "{{ selectedBook?.title }}"? This action cannot be
                undone.
            </template>
            <template #footer>
                <button @click="showDeleteModal = false" type="button"
                        class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2c3e50]">
                    Cancel
                </button>
                <button @click="deleteBook" type="button"
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
    books: Object,
    filters: Object
});

const search = ref(props.filters.search);
const statusFilter = ref(props.filters.status);
const showDeleteModal = ref(false);
const selectedBook = ref(null);

watch([search, statusFilter], debounce(([searchValue, statusValue]) => {
    router.get('/admin/books', {
        search: searchValue,
        status: statusValue
    }, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
        only: ['books', 'filters']
    });
}, 300));

const confirmDelete = (book) => {
    selectedBook.value = book;
    showDeleteModal.value = true;
};

const deleteBook = () => {
    router.delete(`/admin/books/${selectedBook.value.id}`, {
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
        only: ['books'],
        onSuccess: () => {
            router.reload({only: ['books']});
        }
    });
};
</script>
