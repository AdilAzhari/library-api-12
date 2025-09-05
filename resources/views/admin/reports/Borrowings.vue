<template>
    <AdminLayout>
        <div class="px-4 py-6 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-xl font-semibold text-gray-900">Borrowings Report</h1>
                    <p class="mt-2 text-sm text-gray-700">
                        Showing borrowings from {{ filters.start_date }} to {{ filters.end_date }}
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none flex space-x-2">
                    <button
                        @click="exportCSV"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Export CSV
                    </button>
                    <button
                        @click="exportPDF"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Export PDF
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="mt-6 bg-white shadow p-4 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="date_range" class="block text-sm font-medium text-gray-700">Date Range</label>
                        <select
                            id="date_range"
                            v-model="filters.date_range"
                            @change="updateDateRange"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                        >
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>

                    <div v-if="filters.date_range === 'custom'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input
                                type="date"
                                id="start_date"
                                v-model="filters.start_date"
                                @change="updateCustomDates"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            />
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input
                                type="date"
                                id="end_date"
                                v-model="filters.end_date"
                                @change="updateCustomDates"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white p-4 rounded-lg shadow lg:col-span-2">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Borrowings and Returns</h2>
                    <BarChart :chartData="reportData.chart" />
                </div>

                <div class="bg-white p-4 rounded-lg shadow">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Top Borrowed Books</h2>
                    <ul class="divide-y divide-gray-200">
                        <li v-for="(book, index) in reportData.topBooks" :key="book.title" class="py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 bg-indigo-500 text-white rounded-full w-8 h-8 flex items-center justify-center">
                                    {{ index + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ book.title }}</p>
                                    <p class="text-sm text-gray-500 truncate">{{ book.author }}</p>
                                </div>
                                <div class="inline-flex items-center text-sm text-gray-500">
                                    {{ book.borrow_count }} {{ book.borrow_count === 1 ? 'borrowing' : 'borrowings' }}
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Borrowings Table -->
            <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrowed</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Returned</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="borrowing in reportData.borrowings.data" :key="borrowing.id">
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ borrowing.returned_at ? formatDate(borrowing.returned_at) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="statusClasses(borrowing.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ borrowing.status }}
                  </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="reportData.borrowings.links" class="mt-4 px-6 py-3" />
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BarChart from '@/Components/Charts/BarChart.vue';
import Pagination from '@/Components/Pagination.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    reportData: Object,
    filters: Object,
});

const statusClasses = (status) => {
    switch (status.toLowerCase()) {
        case 'borrowed':
            return 'bg-yellow-100 text-yellow-800';
        case 'returned':
            return 'bg-green-100 text-green-800';
        case 'overdue':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};

const updateDateRange = () => {
    router.get(route('admin.reports.borrowings'), {
        date_range: props.filters.date_range,
        start_date: props.filters.start_date,
        end_date: props.filters.end_date,
    }, {
        preserveState: true,
        replace: true,
    });
};

const updateCustomDates = () => {
    if (props.filters.date_range === 'custom') {
        updateDateRange();
    }
};

const exportCSV = () => {
    window.location.href = route('admin.reports.export.borrowings.csv', {
        start_date: props.filters.start_date,
        end_date: props.filters.end_date,
    });
};

const exportPDF = () => {
    window.location.href = route('admin.reports.export.borrowings.pdf', {
        start_date: props.filters.start_date,
        end_date: props.filters.end_date,
    });
};
</script>
